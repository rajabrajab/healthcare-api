<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LifestyleLog;
use Carbon\Carbon;

class LifestyleLogSeeder extends Seeder
{
    public function run()
    {
        $lifestyleBehaviors = [
            [
                'id' => 1,
                'name' => 'Sleep',
                'unit' => 'hrs',
                'value_range' => [4, 10],
                'gdf15_range' => [0, 10],
            ],
            [
                'id' => 2,
                'name' => 'Physical Activity',
                'unit' => 'mins',
                'value_range' => [0, 120],
                'gdf15_range' => [10, 0],
            ],
            [
                'id' => 3,
                'name' => 'Stress',
                'unit' => 'level',
                'enum_values' => ["None", "Occasional", "Frequent"],
                'gdf15_effects' => [0, 5, 10],
            ],
            [
                'id' => 4,
                'name' => 'Alcohol',
                'unit' => 'drinks/week',
                'value_range' => [0, 21],
                'gdf15_range' => [0, 10],
            ],
            [
                'id' => 5,
                'name' => 'Smoking',
                'unit' => 'status',
                'enum_values' => ["Non-smoker", "Occasional", "Regular"],
                'gdf15_effects' => [0, 5, 10],
            ],
            [
                'id' => 6,
                'name' => 'Hydration',
                'unit' => 'L',
                'value_range' => [0.5, 4],
                'gdf15_range' => [10, 0],
            ],
            [
                'id' => 7,
                'name' => 'Meal Timing',
                'unit' => 'time',
                'enum_values' => ["Before 8pm", "8–10pm", "After 10pm"],
                'gdf15_effects' => [0, 5, 10],
            ],
        ];

        $entriesPerBehavior = 10;
        $startDate = Carbon::now()->subMonth();
        $endDate = Carbon::now();

        foreach ($lifestyleBehaviors as $behavior) {
            for ($i = 0; $i < $entriesPerBehavior; $i++) {
                $loggedAt = $this->randomDateBetween($startDate, $endDate);

                if (isset($behavior['enum_values'])) {

                    $randomIndex = array_rand($behavior['enum_values']);
                    $value = $behavior['enum_values'][$randomIndex];
                    $gdf15Effect = $behavior['gdf15_effects'][$randomIndex];
                } else {

                    $value = rand($behavior['value_range'][0] * 10, $behavior['value_range'][1] * 10) / 10;

                    $range = $behavior['value_range'][1] - $behavior['value_range'][0];
                    $position = ($value - $behavior['value_range'][0]) / $range;
                    $gdf15Effect = $behavior['gdf15_range'][0] +
                                   ($behavior['gdf15_range'][1] - $behavior['gdf15_range'][0]) * $position;
                    $gdf15Effect = round($gdf15Effect);
                }

                LifestyleLog::create([
                    'user_id' => 1,
                    'life_style_behavior_id' => $behavior['id'],
                    'value' => $value,
                    'total_gdf15_effect' => $gdf15Effect,
                    'logged_at' => $loggedAt,
                ]);
            }
        }
    }

    private function randomDateBetween(Carbon $startDate, Carbon $endDate): Carbon
    {
        $randomTimestamp = mt_rand($startDate->timestamp, $endDate->timestamp);
        return Carbon::createFromTimestamp($randomTimestamp);
    }
}
