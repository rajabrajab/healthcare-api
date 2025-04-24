<?php

namespace App\Services;

use App\Models\ReadingLog;

class ReadingLogService
{
    public function store($data)
    {
        return ReadingLog::create($data);
    }

    public function getReadingsByDate(string $date)
    {
        $readings = ReadingLog::whereDate('reading_date', $date)
            ->orderBy('reading_time')
            ->get(['reading_time', 'reading'])
            ->map(function ($log) {
                return [
                    'time' => $log->formatted_time,
                    'reading' => $log->reading,
                ];
        });

        if ($readings->isEmpty()) {
            return [
                'time' => 0,
                'reading' => 0
            ];
        }

        return $readings;
    }

    public function getStatisticsByDate()
    {
        $readings = ReadingLog::get()->pluck('reading');

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
