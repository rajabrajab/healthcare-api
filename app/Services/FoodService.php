<?php

namespace App\Services;

use App\Http\Resources\CategoryWithFoods;
use App\Http\Resources\FavoriteFoodCategoryResource;
use App\Models\FoodCategory;
use Illuminate\Support\Facades\Auth;

class FoodService
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function getFoodsGroupedByCategory()
    {
        $categories = FoodCategory::with('foods')->get();

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
}
