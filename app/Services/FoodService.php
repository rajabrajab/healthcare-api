<?php

namespace App\Services;

use App\Models\FoodCategory;

class FoodService
{
    public function getFoodsGroupedByCategory(): array
    {
        $categories = FoodCategory::with('foods')->get();

        $result = [];

        foreach ($categories as $category) {
            $result[$category->name] = $category->foods->map(function ($food) {
                return [
                    'id' => $food->id,
                    'name' => $food->name,
                ];
            })->toArray();
        }

        return $result;
    }
}
