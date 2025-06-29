<?php

namespace Database\Seeders;

use App\Models\Food;
use App\Models\FoodCategory;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Vegetables',
                'measurement_unit' => 'g',
                'gdf15_effect' => 'Anti-inflammatory',
                'gdf15_points' => 0,
                'foods' => ['Spinach', 'Kale', 'Broccoli', 'Carrots'],
            ],
            [
                'name' => 'Fruits',
                'measurement_unit' => 'g',
                'gdf15_effect' => 'Anti-inflammatory',
                'gdf15_points' => 0,
                'foods' => ['Berries', 'Apples', 'Oranges', 'Pomegranates'],
            ],
            [
                'name' => 'Fats',
                'measurement_unit' => 'g',
                'gdf15_effect' => 'Anti-inflammatory',
                'gdf15_points' => 0,
                'foods' => ['Olive oil', 'Avocados', 'Omega-3 oils'],
            ],
            [
                'name' => 'Protein',
                'measurement_unit' => 'g',
                'gdf15_effect' => 'Anti-inflammatory',
                'gdf15_points' => 0,
                'foods' => ['Fatty fish', 'Legumes', 'Tofu'],
            ],
            [
                'name' => 'Whole Grains',
                'measurement_unit' => 'g',
                'gdf15_effect' => 'Anti-inflammatory',
                'gdf15_points' => 0,
                'foods' => ['Quinoa', 'Oats', 'Barley'],
            ],
            [
                'name' => 'Spices & Herbs',
                'measurement_unit' => 'g',
                'gdf15_effect' => 'Anti-inflammatory',
                'gdf15_points' => 0,
                'foods' => ['Turmeric', 'Ginger', 'Garlic'],
            ],
            [
                'name' => 'Others',
                'measurement_unit' => 'ml',
                'gdf15_effect' => 'Anti-inflammatory',
                'gdf15_points' => 0,
                'foods' => ['Green tea', 'Kefir', 'Fermented foods'],
            ],
            [
                'name' => 'Grains & Carbs',
                'measurement_unit' => 'g',
                'gdf15_effect' => 'Mixed',
                'gdf15_points' => 5,
                'foods' => ['White rice', 'Pasta', 'Corn'],
            ],
            [
                'name' => 'Dairy',
                'measurement_unit' => 'ml',
                'gdf15_effect' => 'Mixed',
                'gdf15_points' => 5,
                'foods' => ['Milk', 'Cheese', 'Yogurt (sweetened)'],
            ],
            [
                'name' => 'Animal Protein',
                'measurement_unit' => 'g',
                'gdf15_effect' => 'Mixed',
                'gdf15_points' => 5,
                'foods' => ['Chicken', 'Eggs', 'Lean beef'],
            ],
            [
                'name' => 'Snacks',
                'measurement_unit' => 'g',
                'gdf15_effect' => 'Mixed',
                'gdf15_points' => 5,
                'foods' => ['Nut butters (with additives)', 'Protein bars'],
            ],
            [
                'name' => 'Processed Meats',
                'measurement_unit' => 'g',
                'gdf15_effect' => 'Pro-inflammatory',
                'gdf15_points' => 10,
                'foods' => ['Bacon', 'Sausage', 'Deli meats'],
            ],
            [
                'name' => 'Fried & Fast Food',
                'measurement_unit' => 'g',
                'gdf15_effect' => 'Pro-inflammatory',
                'gdf15_points' => 10,
                'foods' => ['French fries', 'Fried chicken', 'Burgers'],
            ],
            [
                'name' => 'Sugary & Refined Carbs',
                'measurement_unit' => 'g',
                'gdf15_effect' => 'Pro-inflammatory',
                'gdf15_points' => 10,
                'foods' => ['Soda', 'Pastries', 'Cookies', 'White bread'],
            ],
            [
                'name' => 'Additives & Preserved',
                'measurement_unit' => 'g',
                'gdf15_effect' => 'Pro-inflammatory',
                'gdf15_points' => 10,
                'foods' => ['MSG', 'Nitrates', 'Artificial sweeteners'],
            ],
            [
                'name' => 'Beverages',
                'measurement_unit' => 'ml',
                'gdf15_effect' => 'Pro-inflammatory',
                'gdf15_points' => 10,
                'foods' => ['Sugary juices', 'Beer', 'Sweet cocktails'],
            ],
            [
                'name' => 'Custom Foods',
                'measurement_unit' => 'g',
                'gdf15_effect' => 'Anti-inflammatory',
                'gdf15_points' =>  0,
                'foods' => []
            ]
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
                    'description' => $foodName . ' - no description yet.',
                    'category_id' => $category->id,
                ]);
            }
        }
    }
}
