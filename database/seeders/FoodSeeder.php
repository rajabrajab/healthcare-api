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
                'foods' => [
                    'Apple' => 'A sweet and crisp fruit, perfect for snacks or desserts.',
                    'Banana' => 'A soft and energy-rich fruit, great for smoothies.',
                    'Orange' => 'A juicy citrus fruit, high in vitamin C.',
                ],
            ],
            [
                'name' => 'Vegetables',
                'measurement_unit' => 'g',
                'gdf15_effect' => 'none',
                'gdf15_points' => 0,
                'foods' => [
                    'Apple' => 'A sweet and crisp fruit, perfect for snacks or desserts.',
                    'Banana' => 'A soft and energy-rich fruit, great for smoothies.',
                    'Orange' => 'A juicy citrus fruit, high in vitamin C.',
                ],
            ],
            [
                'name' => 'Dairy',
                'measurement_unit' => 'ml',
                'gdf15_effect' => 'decrease',
                'gdf15_points' => -5,
                'foods' => [
                    'Milk' => 'A nutritious liquid dairy product rich in calcium.',
                    'Cheese' => 'A solid dairy product made from milk, rich in protein and fat.',
                ],
            ],
        ];

        foreach ($categories as $data) {
            $category = FoodCategory::create([
                'name' => $data['name'],
                'measurement_unit' => $data['measurement_unit'],
                'gdf15_effect' => $data['gdf15_effect'],
                'gdf15_points' => $data['gdf15_points'],
            ]);

            foreach ($data['foods'] as $foodName => $description) {
                Food::create([
                    'name' => $foodName,
                    'description' => $description,
                    'category_id' => $category->id,
                ]);
            }
        }
    }
}
