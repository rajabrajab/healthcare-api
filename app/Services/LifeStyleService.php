<?php

namespace App\Services;

use App\Models\LifeStyleBehavior;
use App\Models\LifeStyleLog;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
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
        $date = Date::now()->toDateString();

        foreach ($lifestylies as $entry) {
            $behavior = LifeStyleBehavior::findOrFail($entry['life_style_behavior_id']);
            $value = $entry['value'];
            $effect = $this->calculateGdf15Effect($behavior, $value);

            $loggedAt = Carbon::parse($date)
            ->setTime(
                now()->hour,
                now()->minute,
                now()->second
            );

            $log = LifeStyleLog::create([
                'user_id' => $this->user->id,
                'life_style_behavior_id' => $behavior->id,
                'value' => $value,
                'total_gdf15_effect' => $effect,
                'logged_at' => $loggedAt,
            ]);

            $logs[] = $log;
        }

        return $logs;
    }

    public function updateLifeStyleLog(array $lifestylies, $date)
    {
        $date = Carbon::parse($date)->toDateString();
        $userId = $this->user->id;

        $updatedLogs = [];

        foreach ($lifestylies as $entry) {
            $log = LifeStyleLog::where('user_id', $userId)
                ->where('life_style_behavior_id', $entry['life_style_behavior_id'])
                ->whereDate('logged_at', $date)
                ->first();

            $behavior = LifeStyleBehavior::findOrFail($entry['life_style_behavior_id']);

            if ($log) {
                $log->value = $entry['value'];
                $log->total_gdf15_effect = $this->calculateGdf15Effect($behavior, $entry['value']);
                $log->save();
            } else {
                $log = LifeStyleLog::create([
                    'user_id' => $userId,
                    'life_style_behavior_id' => $entry['life_style_behavior_id'],
                    'value' => $entry['value'],
                    'total_gdf15_effect' => $this->calculateGdf15Effect($behavior, $entry['value']),
                    'logged_at' => $date,
                ]);
            }

            $updatedLogs[] = $log;
        }

        return $updatedLogs;
    }

    public function getLifeStyleScoreByPeriod($startDate = null, $endDate = null)
    {
        // $date = Carbon::parse($date);

        // switch ($type) {
        //     case 'week':
        //         $start = $date->copy()->startOfWeek()->startOfDay();
        //         $end =  $date->copy()->endOfWeek()->endOfDay();
        //         $select = "DAYNAME(logged_at) as time";
        //         $groupBy = "DAYNAME(logged_at)";
        //         $orderBy = "DAYOFWEEK(logged_at)";
        //         break;

        //     case 'month':
        //         $start = $date->copy()->startOfMonth()->startOfDay();
        //         $end = $date->copy()->endOfMonth()->endOfDay();
        //         $select = "DAY(logged_at) as time";
        //         $groupBy = "DAY(logged_at)";
        //         $orderBy = "DAY(logged_at)";
        //         break;

        //     case 'day':
        //     default:
        //         $start = $date->copy()->startOfDay();
        //         $end = $date->copy()->endOfDay();
        //         $select = "DATE_FORMAT(logged_at, '%h:%i %p') as time";
        //         $groupBy = "time";
        //         $orderBy = "STR_TO_DATE(time, '%h:%i %p')";
        //         break;
        // }

        // return LifeStyleLog::where('user_id', $this->user->id)
        //     ->whereBetween('logged_at', [$start, $end])
        //     ->selectRaw("$select, SUM(total_gdf15_effect) as points")
        //     ->groupByRaw($groupBy)
        //     ->orderByRaw($orderBy)
        //     ->get()
        //     ->toArray();

        $endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::today()->endOfDay();
        $startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : $endDate->copy()->startOfDay();

        $results = LifeStyleLog::where('user_id', $this->user->id)
            ->whereBetween('logged_at', [$startDate, $endDate])
            ->selectRaw("
                DATE(logged_at) as date,
                SUM(total_gdf15_effect) as points
            ")
            ->groupBy('date')
            ->orderByDesc('date')
            ->take(6)
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'points' => (float) $item->points ?: 0
                ];
            })
            ->toArray();

        if (empty($results)) {
            return [['date' => $endDate->format('Y-m-d'), 'points' => 0]];
        }

        return $results;
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
                    // 'after_10pm_with_snack' => 10,
                    default => 0,
                };

            default:
                return 0;
        }
    }

    public function getLifeStyleLog($date)
    {
        $day = Carbon::parse($date)->toDateString();

        $allLifeStyles = LifeStyleBehavior::all()->initializeEnumValues();

        $loggedActivities = $this->user->userLifeStyleLogs()
            ->with('lifeStyle')
            ->whereDate('logged_at', $day)
            ->get()
            ->keyBy('lifeStyle.id');

        $result = $allLifeStyles->map(function ($lifeStyle) use ($loggedActivities) {
            $log = $loggedActivities->get($lifeStyle->id);

            return [
                'id' => $lifeStyle->id,
                'name' => $lifeStyle->name,
                'unit' => $lifeStyle->unit,
                'value' => $log ? $log->value : null,
                'enum_values' => $lifeStyle->enum_values
            ];
        })->values();
        return $result;
    }

    public function getPhysicalActivityMinutes($userId)
    {
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        return (int) LifestyleLog::where('user_id', $userId)
            ->where('life_style_behavior_id', 2)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->sum('value');
    }
}
