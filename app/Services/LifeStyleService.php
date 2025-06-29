<?php

namespace App\Services;

use App\Models\LifeStyleBehavior;
use App\Models\LifeStyleLog;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LifeStyleService
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function getLifeStyle()
    {
        return LifeStyleBehavior::get()->initializeEnumValues();
    }

    public function logLifeStyle(array $lifestylies)
    {
        $logs = [];

        foreach ($lifestylies as $entry) {
            $behavior = LifeStyleBehavior::findOrFail($entry['life_style_behavior_id']);
            $value = $entry['value'];
            $effect = $this->calculateGdf15Effect($behavior, $value);

            $log = LifeStyleLog::create([
                'user_id' => $this->user->id,
                'life_style_behavior_id' => $behavior->id,
                'value' => $value,
                'total_gdf15_effect' => $effect,
                'logged_at' => Carbon::now(),
            ]);

            $logs[] = $log;
        }

        return $logs;
    }

    public function getLifeStyleScoreByPeriod($date, $type = 'day'): array
    {
        $date = Carbon::parse($date);

        switch ($type) {
            case 'week':
                $start = $date->copy()->startOfWeek()->startOfDay();
                $end =  $date->copy()->endOfWeek()->endOfDay();
                $select = "DAYNAME(logged_at) as time";
                $groupBy = "DAYNAME(logged_at)";
                $orderBy = "DAYOFWEEK(logged_at)";
                break;

            case 'month':
                $start = $date->copy()->startOfMonth()->startOfDay();
                $end = $date->copy()->endOfMonth()->endOfDay();
                $select = "DAY(logged_at) as time";
                $groupBy = "DAY(logged_at)";
                $orderBy = "DAY(logged_at)";
                break;

            case 'day':
            default:
                $start = $date->copy()->startOfDay();
                $end = $date->copy()->endOfDay();
                $select = "DATE_FORMAT(logged_at, '%h:%i %p') as time";
                $groupBy = "time";
                $orderBy = "STR_TO_DATE(time, '%h:%i %p')";
                break;
        }

        return LifeStyleLog::where('user_id', $this->user->id)
            ->whereBetween('logged_at', [$start, $end])
            ->selectRaw("$select, SUM(total_gdf15_effect) as points")
            ->groupByRaw($groupBy)
            ->orderByRaw($orderBy)
            ->get()
            ->toArray();
    }

    protected function calculateGdf15Effect($behavior, $value): int
    {
        $name = strtolower($behavior->name);

        switch ($name) {
            case 'sleep':
                $hrs = floatval($value);
                return ($hrs >= 7 && $hrs <= 8) ? 0 : (($hrs >= 6 && $hrs < 7) ? 5 : 10);

            case 'physical activity':
                $mins = floatval($value);
                return ($mins > 30) ? 0 : (($mins >= 20) ? 5 : 10);

            case 'stress':
                return match (strtolower($value)) {
                    'none' => 0,
                    'occasional' => 5,
                    'frequent' => 10,
                    default => 10,
                };

            case 'alcohol':
                $drinks = intval($value);
                return ($drinks === 0) ? 0 : (($drinks <= 5) ? 5 : 10);

            case 'smoking':
                return match (strtolower($value)) {
                    'non-smoker' => 0,
                    'occasional' => 5,
                    'regular' => 10,
                    default => 10,
                };

            case 'hydration':
                $liters = floatval($value);
                return ($liters >= 1.5) ? 0 : (($liters >= 1.0) ? 5 : 10);

            case 'meal timing':
                return match (strtolower($value)) {
                    'before_8pm' => 0,
                    '8_to_10pm' => 5,
                    'after_10pm' => 10,
                    'after_10pm_with_snack' => 10,
                    default => 0,
                };

            default:
                return 0;
        }
    }

    public function getLifeStyleLog($date)
    {
        $day = Carbon::parse($date)->toDateString();

        $logs = $this->user->userLifeStyleLogs()
                ->with('lifeStyle')
                ->whereDate('logged_at',$day)
                ->get();

        $logs = $logs->map(function ($log) {

            if (!$log->lifeStyle) return null;

            return [
                'name' => $log->lifeStyle->name,
                'unit' => $log->lifeStyle->unit,
                'value' => $log->value
            ];
        })->filter()->values();


        return $logs;
    }
}
