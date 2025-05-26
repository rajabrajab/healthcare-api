<?php

namespace App\Http\Controllers;

use App\Models\Gdf15Tracking;
use App\Services\FoodService;
use App\Services\LifeStyleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Gdf15StatisticsController extends Controller
{

    protected $foodService;
    protected $lifeStyleService;

    public function __construct(FoodService $foodService, LifeStyleService $lifeStyleService)
    {
        $this->foodService = $foodService;
        $this->lifeStyleService = $lifeStyleService;
    }


    public function byDate(Request $request)
    {
        $userId = Auth::id();
        $date = $request->input('date', Carbon::today()->toDateString());

        $foodStats = $this->foodService->getDailyDietScoreByHour($date);
        $lifestyleStats = $this->lifeStyleService->getDailyLifeStyleScoreByHour($date);

        $tracking = Gdf15Tracking::where('user_id', $userId)
            ->where('tracking_date', $date)
            ->first();

        $trackingStats = [];

        if ($tracking) {
            $trackingStats[] = [
                'time' => Carbon::parse($tracking->created_at)->format('h:i A'),
                'points' => $tracking->total_gdf15_effect,
            ];
        }

        return response()->data([
            'food_log_stats' => $foodStats,
            'lifestyle_log_stats' => $lifestyleStats,
            'gdf15_tracking_stats' => $trackingStats,
        ]);
    }
}
