<?php

namespace App\Services;

use App\Http\Resources\CategoryWithFoods;
use App\Http\Resources\FavoriteFoodCategoryResource;
use App\Http\Resources\FoodLogCategoryResource;
use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\FoodLog;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FoodService
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function getFoodsGroupedByCategory(?string $search = null)
    {
        $categories = FoodCategory::with(['foods' => function($query) use ($search) {
            if ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            }
        }])->get();

        return CategoryWithFoods::collection($categories);
    }

    public function getUserDiet()
    {
        $favorites = $this->user->userDiet()->with('category')->get();

        $grouped = $favorites->groupBy(function ($food) {
            return $food->category->id;
        })->map(function ($foods) {
            return [
                'category' => $foods->first()->category,
                'foods' => $foods,
            ];
        })->values();

        return FavoriteFoodCategoryResource::collection($grouped);
    }


    public function addFoodToUserDiet(array $foodIds)
    {
        return $this->user->userDiet()->syncWithoutDetaching($foodIds);
    }

    public function logFoodIntake(array $foods)
    {
        $createdLogs = [];

        foreach ($foods as $entry) {
            $food = Food::findOrFail($entry['food_id']);

            $totalEffect = $food->category->gdf15_points * $entry['quantity'];

            $log = FoodLog::create([
                'user_id' => $this->user->id,
                'food_id' => $food->id,
                'taken_at' => Carbon::now(),
                'quantity' => $entry['quantity'],
                'total_gdf15_effect' => $totalEffect,
            ]);

            $createdLogs[] = $log;
        }

        return $createdLogs;
    }

    public function getFoodLog($date)
    {
        $day = Carbon::parse($date)->toDateString();

        $logs = $this->user->userFoodLogs()
                ->with('food.category')
                ->whereDate('taken_at',$day)
                ->get();

        $grouped = $logs->groupBy(fn($log) => $log->food->category->id)
                    ->map(function ($logs) {
                        return [
                            'category' => $logs->first()->food->category,
                            'logs' => $logs,
                        ];
                    })
                    ->values();

        return FoodLogCategoryResource::collection($grouped);
    }

    public function getDailyDietScoreByHour($date): array
    {
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->endOfDay();

        return FoodLog::where('user_id', $this->user->id)
            ->whereBetween('taken_at', [$startOfDay, $endOfDay])
            ->selectRaw("DATE_FORMAT(taken_at, '%h:%i %p') as time, SUM(total_gdf15_effect) as points")
            ->groupBy('time')
            ->orderByRaw("STR_TO_DATE(time, '%h:%i %p')")
            ->get()
            ->toArray();
    }

    public function deleteFromUserDiet(int $foodId)
    {
        return $this->user->userDiet()->detach($foodId) > 0;
    }
}
