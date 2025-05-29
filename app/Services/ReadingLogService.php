<?php

namespace App\Services;

use App\Models\ReadingLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReadingLogService
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function store($data)
    {
        return ReadingLog::create($data);
    }

    public function getReadingsByDate(string $date, $type = 'day')
    {
        $date = Carbon::parse($date);
        $userId = $this->user->id;

        switch ($type) {
            case 'week':
                $start = $date->copy()->startOfWeek();
                $end = $date->copy()->endOfWeek();
                $groupFormat = "DAYNAME(reading_date)";
                $orderBy = "FIELD(DAYOFWEEK(reading_date), 1,2,3,4,5,6,7)";
                break;

            case 'month':
                $start = $date->copy()->startOfMonth();
                $end = $date->copy()->endOfMonth();
                $groupFormat = "DAY(reading_date)";
                $orderBy = "DAY(reading_date)";
                break;

            default:
                $start = $date->copy()->startOfDay();
                $end = $date->copy()->endOfDay();
                $groupFormat = "DATE_FORMAT(reading_time, '%h:%i %p')";
                $orderBy = "STR_TO_DATE($groupFormat, '%h:%i %p')";
                break;
        }

        $logs = ReadingLog::where('user_id', $userId)
            ->whereBetween('reading_date', [$start, $end])
            ->orderBy('reading_date')
            ->orderBy('reading_time')
            ->get();

        $grouped = $logs->groupBy(function ($log) use ($type) {
            return match ($type) {
                'week' => Carbon::parse($log->reading_date)->format('l'),
                'month' => Carbon::parse($log->reading_date)->format('j'),
                default => Carbon::parse($log->reading_time)->format('g:i A'),
            };
        });

        // Format result per group
        $result = $grouped->map(function ($items, $key) {
            $first = $items->first();
            return [
                'time' => $key,
                'points' => $items->sum('reading'), // still summed if needed
                'eaze_diabetes' => $first->eaze_diabetes,
                'drug_response' => $first->drug_response,
            ];
        })->values();

        return $result->isEmpty()
            ? [['time' => 0, 'reading' => 0]]
            : $result;
    }

    public function getStatisticsByDate()
    {
        $readings = ReadingLog::where('user_id', $this->user->id)
        ->get()
        ->pluck('reading');

        if ($readings->isEmpty()) {
            return [
                'max' => null,
                'min' => null,
                'avg' => null,
            ];
        }

        return [
            'max' => $readings->max(),
            'min' => $readings->min(),
            'avg' => round($readings->avg(), 2),
        ];
    }

}
