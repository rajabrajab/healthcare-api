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
            ['name' => 'Stress', 'unit' => 'level', 'enum_values' => ['None', 'Occasional', 'Frequent']],
            ['name' => 'Alcohol', 'unit' => 'drinks/week'],
            ['name' => 'Smoking', 'unit' => 'status', 'enum_values' => ['Non-smoker', 'Occasional', 'Regular']],
            ['name' => 'Hydration', 'unit' => 'L'],
            ['name' => 'Meal Timing', 'unit' => 'time', 'enum_values' => ['Before 8pm', '8–10pm', 'After 10pm']],
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
