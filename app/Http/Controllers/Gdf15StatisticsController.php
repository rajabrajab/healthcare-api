<?php

namespace App\Http\Controllers;

use App\Models\Gdf15Tracking;
use App\Services\FoodService;
use App\Services\LifeStyleService;
use App\Services\ReadingLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Gdf15StatisticsController extends Controller
{

    protected $foodService;
    protected $lifeStyleService;
    protected $readingLogService;

    public function __construct(FoodService $foodService, LifeStyleService $lifeStyleService, ReadingLogService $readingLogService)
    {
        $this->foodService = $foodService;
        $this->lifeStyleService = $lifeStyleService;
        $this->readingLogService = $readingLogService;
    }


    public function byDate(Request $request)
    {
        $userId = Auth::id();
        $date = $request->input('date', Carbon::today()->toDateString());
        $fromDate = $request->input('from_date');
        $endDate = $request->input('end_date');
        $type = $request->input('type', 'day');

        $foodStats = $this->foodService->getDietScoreByPeriod($date,$type);
        $lifestyleStats = $this->lifeStyleService->getLifeStyleScoreByPeriod($date,$type);
        $readingStats = $this->readingLogService->getReadingsByDate($fromDate,$endDate);

        $physicalActivityMinutes = $this->lifeStyleService->getPhysicalActivityMinutes($userId, $date, $type);

        $combinedStats = collect();

        foreach ($foodStats as $stat) {
            $combinedStats->push([
                'time' => $stat['time'] ?? null,
                'points' => $stat['points'] ?? 0,
                'type' => 'food',
            ]);
        }

        foreach ($lifestyleStats as $stat) {
            $combinedStats->push([
                'time' => $stat['time'] ?? null,
                'points' => $stat['points'] ?? 0,
                'type' => 'lifestyle'
            ]);
        }

        foreach ($readingStats as $stat) {
            $combinedStats->push([
                'time' => $stat['time'] ?? null,
                'points' => $stat['points'] ?? 0,
                'type' => 'reading'
            ]);
        }


        $combinedStats = $combinedStats->sortBy('time')->values();

        return response()->data([
            'food_log_stats' => $foodStats,
            'lifestyle_log_stats' => $lifestyleStats,
            'gdf15_tracking_stats' => $combinedStats,
            'reading_stats' => $readingStats,
            'physical_activity_minutes' => $physicalActivityMinutes
        ]);
    }
}
