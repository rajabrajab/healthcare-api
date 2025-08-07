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
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

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
        $date = Date::now()->toDateString();

        foreach ($foods as $entry) {
            $totalEffect = $this->calculateGdf15Effect($entry['food_id'],$entry['quantity']);

            $takenAt = Carbon::parse($date)
            ->setTime(
                now()->hour,
                now()->minute,
                now()->second
            );

            $log = FoodLog::create([
                'user_id' => $this->user->id,
                'food_id' => $entry['food_id'],
                'taken_at' => $takenAt,
                'quantity' => $entry['quantity'],
                'total_gdf15_effect' => $totalEffect,
            ]);

            $createdLogs[] = $log;
        }

        return $createdLogs;
    }

   public function updateFoodLog(array $foods, $date)
    {
        $date = Carbon::parse($date)->toDateString();
        $userId = $this->user->id;

        $updatedLogs = [];

        foreach ($foods as $entry) {
            $log = FoodLog::where('user_id', $userId)
                ->where('food_id', $entry['food_id'])
                ->whereDate('taken_at', $date)
                ->first();

            if ($log) {
                $log->quantity = $entry['quantity'];
                $log->total_gdf15_effect = $this->calculateGdf15Effect($entry['food_id'], $entry['quantity']);
                $log->save();
            } else {
                $log = FoodLog::create([
                    'user_id' => $userId,
                    'food_id' => $entry['food_id'],
                    'quantity' => $entry['quantity'],
                    'total_gdf15_effect' => $this->calculateGdf15Effect($entry['food_id'], $entry['quantity']),
                    'taken_at' => $date,
                ]);
            }

            $updatedLogs[] = $log;
        }

        return $updatedLogs;
    }


    public function getFoodLog($date)
    {
        $day = Carbon::parse($date)->toDateString();

        $dietFoods = $this->user->userDiet()
            ->with('category')
            ->get();

        $loggedFoods = $this->user->userFoodLogs()
            ->with('food.category')
            ->whereDate('taken_at', $day)
            ->get()
            ->keyBy('food_id');

        $combined = $dietFoods->map(function ($food) use ($loggedFoods) {
            $log = $loggedFoods->get($food->id);

            return [
                'category' => $food->category,
                'log' => $log ?: new FoodLog([
                    'food' => $food,
                    'quantity' => null
                ])
            ];
        });

        $grouped = $combined->groupBy(function ($item) {
            return $item['category']->id;
        })->map(function ($items, $categoryId) {
            return [
                'category' => $items->first()['category'],
                'logs' => $items->pluck('log')
            ];
        })->values();

        return FoodLogCategoryResource::collection($grouped);
    }

    public function getDietScoreByPeriod($startDate = null, $endDate = null): array
    {
        $endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::today()->endOfDay();
        $startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : $endDate->copy()->startOfDay();

        $results = FoodLog::where('user_id', $this->user->id)
            ->whereBetween('taken_at', [$startDate, $endDate])
            ->selectRaw("
                DATE(taken_at) as date,
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
    public function deleteFromUserDiet(int $foodId)
    {
        return $this->user->userDiet()->detach($foodId) > 0;
    }

    public function createCustomFood($data)
    {
        $customCategory = FoodCategory::where('name', 'Custom Foods')->firstOrFail();

        $foodData = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'category_id' => $customCategory->id,
            'user_id' => $this->user->id,
            'gdf15_points' => $data['gdf15_points'] ?? $customCategory->gdf15_points,
        ];

        return Food::create($foodData);
    }

    private function calculateGdf15Effect($foodId,$quantity)
    {
        $food = Food::findOrFail($foodId);

        $gdf15Points = $food->gdf15_points ?? $food->category->gdf15_points;

        return $gdf15Points * $quantity;
    }
}
