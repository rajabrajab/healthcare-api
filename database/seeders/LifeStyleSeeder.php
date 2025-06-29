<?php

namespace Database\Seeders;
use App\Models\LifeStyleBehavior;

use Illuminate\Database\Seeder;

class LifeStyleSeeder extends Seeder
{
    public function run()
    {
        $behaviors = [
            ['name' => 'Sleep', 'unit' => 'hrs'],
            ['name' => 'Physical Activity', 'unit' => 'mins'],
            [
                'name' => 'Stress',
                'unit' => 'level',
                'enum_values' => [
                    ['label' => 'None', 'value' => 'none'],
                    ['label' => 'Occasional', 'value' => 'occasional'],
                    ['label' => 'Frequent', 'value' => 'frequent']
                ]
            ],
            ['name' => 'Alcohol', 'unit' => 'drinks/day'],
            [
                'name' => 'Smoking',
                'unit' => 'status',
                'enum_values' => [
                    ['label' => 'Non-smoker', 'value' => 'non_smoker'],
                    ['label' => 'Occasional', 'value' => 'occasional'],
                    ['label' => 'Regular', 'value' => 'regular']
                ]
            ],
            ['name' => 'Hydration', 'unit' => 'L'],
            [
                'name' => 'Meal Timing',
                'unit' => 'time',
                'enum_values' => [
                    ['label' => 'Before 8pm', 'value' => 'before_8pm'],
                    ['label' => '8–10pm', 'value' => '8_to_10pm'],
                    ['label' => 'After 10pm', 'value' => 'after_10pm'],
                    ['label' => 'After 10pm + ( Late snack )', 'value' => 'after_10pm_with_snack']
                ]
            ],
        ];

        foreach ($behaviors as $b) {
            LifeStyleBehavior::create([
                'name' => $b['name'],
                'unit' => $b['unit'],
                'enum_values' => isset($b['enum_values']) ? json_encode($b['enum_values']) : null,
            ]);
        }
    }
}
