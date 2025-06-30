<?php

namespace App\Http\Controllers;

use App\Constants\ResponseMessages;
use App\Models\ReadingLog;
use App\Services\ReadingLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'reading_time' => 'nullable|date_format:H:i',
            'reading' => 'required|integer',
            'drug_response' => 'nullable|string',
            'eaze_diabetes' => 'required|string',
        ]);

        $data['user_id'] = Auth::id();

        $log = $this->readingLogService->store($data);

        return response()->data($log,ResponseMessages::CREATE_SUCCESS);

    }

    public function readingsByDate(Request $request)
    {
        $type = $request->query('type', 'day');
        $date = $request->query('date', now()->toDateString());

        $readings = $this->readingLogService->getReadingsByDate($date,$type);

        return response()->data($readings,ResponseMessages::INDEX_SUCCESS);
    }

    public function statistics()
    {
        $stats = $this->readingLogService->getStatisticsByDate();

        return response()->data($stats,ResponseMessages::INDEX_SUCCESS);
    }

}
