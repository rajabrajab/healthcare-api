<?php

namespace App\Services;

use App\Models\ReadingLog;
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

    public function getReadingsByDate(string $date)
    {
        $userId = $this->user->id;

        $readings = ReadingLog::where('user_id', $userId)
            ->whereDate('reading_date', $date)
            ->orderBy('reading_time')
            ->get(['reading_time', 'reading','eaze_diabetes','drug_response'])
            ->map(function ($log) {
                return [
                    'time' => $log->formatted_time,
                    'reading' => $log->reading,
                    'eaze_diabetes' => $log->eaze_diabetes,
                    'drug_response' => $log->drug_response,
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
