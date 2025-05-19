<?php

namespace Database\Seeders;

use App\Models\Food;
use App\Models\FoodCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Fruits',
                'measurement_unit' => 'g',
                'gdf15_effect' => 'increase',
                'gdf15_points' => 10,
                'foods' => ['Apple', 'Banana', 'Orange'],
            ],
            [
                'name' => 'Vegetables',
                'measurement_unit' => 'g',
                'gdf15_effect' => 'none',
                'gdf15_points' => 0,
                'foods' => ['Carrot', 'Broccoli'],
            ],
            [
                'name' => 'Dairy',
                'measurement_unit' => 'ml',
                'gdf15_effect' => 'decrease',
                'gdf15_points' => -5,
                'foods' => ['Milk', 'Cheese'],
            ],
        ];

        foreach ($categories as $data) {
            $category = FoodCategory::create([
                'name' => $data['name'],
                'measurement_unit' => $data['measurement_unit'],
                'gdf15_effect' => $data['gdf15_effect'],
                'gdf15_points' => $data['gdf15_points'],
            ]);

            foreach ($data['foods'] as $foodName) {
                Food::create([
                    'name' => $foodName,
                    'category_id' => $category->id,
                ]);
            }
        }
    }
}
