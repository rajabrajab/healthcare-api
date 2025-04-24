<?php

namespace App\Http\Controllers;

use App\Constants\ResponseMessages;
use App\Models\ReadingLog;
use App\Services\ReadingLogService;
use Illuminate\Http\Request;

class ReadingLogsControllr extends Controller
{
    private $readingLogService;

    public function __construct(ReadingLogService $readingLogService)
    {
        $this->readingLogService = $readingLogService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'reading_date' => 'required|date',
            'reading_time' => 'required|date_format:H:i',
            'reading' => 'required|integer',
        ]);

        $log = $this->readingLogService->store($data);

        return response()->data($log,ResponseMessages::CREATE_SUCCESS);

    }

    public function readingsByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $readings = $this->readingLogService->getReadingsByDate($request->date);

        return response()->data($readings,ResponseMessages::INDEX_SUCCESS);
    }

    public function statistics()
    {
        $stats = $this->readingLogService->getStatisticsByDate();

        return response()->data($stats,ResponseMessages::INDEX_SUCCESS);
    }

}
