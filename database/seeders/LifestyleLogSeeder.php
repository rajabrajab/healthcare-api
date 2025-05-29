<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LifeStyleLog;
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
                'gdf15_effects' => [0, 5, 10],
            ],
            [
                'id' => 2,
                'name' => 'Physical Activity',
                'unit' => 'mins',
                'value_range' => [0, 120],
                'gdf15_effects' => [0, 5, 10],
            ],
            [
                'id' => 3,
                'name' => 'Stress',
                'unit' => 'level',
                'enum_values' => ["none", "occasional", "frequent"],
                'gdf15_effects' => [0, 5, 10],
            ],
            [
                'id' => 4,
                'name' => 'Alcohol',
                'unit' => 'drinks/week',
                'value_range' => [0, 21],
                'gdf15_effects' => [0, 5, 10],
            ],
            [
                'id' => 5,
                'name' => 'Smoking',
                'unit' => 'status',
                'enum_values' => ["non-smoker", "occasional", "regular"],
                'gdf15_effects' => [0, 5, 10],
            ],
            [
                'id' => 6,
                'name' => 'Hydration',
                'unit' => 'L',
                'value_range' => [0.5, 4],
                'gdf15_effects' => [0, 5, 10],
            ],
            [
                'id' => 7,
                'name' => 'Meal Timing',
                'unit' => 'time',
                'enum_values' => ["before_8pm", "8–10pm", "after_10pm"],
                'gdf15_effects' => [0, 5, 10],
            ],
        ];

        // Regular random entries (last month)
        $entriesPerBehavior = 40; // Reduced from 50 to make room for April entries
        $startDate = Carbon::now()->subMonth();
        $endDate = Carbon::now();

        // April-specific entries
        $aprilEntriesPerBehavior = 10;
        $aprilStart = Carbon::create(null, 4, 1); // April 1st of current year
        $aprilEnd = Carbon::create(null, 4, 30); // April 30th of current year

        foreach ($lifestyleBehaviors as $behavior) {
            // Create regular entries
            for ($i = 0; $i < $entriesPerBehavior; $i++) {
                $this->createLogEntry($behavior, $startDate, $endDate);
            }

            // Create April-specific entries
            for ($i = 0; $i < $aprilEntriesPerBehavior; $i++) {
                $this->createLogEntry($behavior, $aprilStart, $aprilEnd);
            }
        }
    }

    private function createLogEntry(array $behavior, Carbon $startDate, Carbon $endDate): void
    {
        $loggedAt = $this->randomDateBetween($startDate, $endDate);

        if (isset($behavior['enum_values'])) {
            $value = $behavior['enum_values'][array_rand($behavior['enum_values'])];
        } else {
            $value = rand($behavior['value_range'][0] * 10, $behavior['value_range'][1] * 10) / 10;
        }

        $gdf15Effect = $behavior['gdf15_effects'][array_rand($behavior['gdf15_effects'])];

        LifeStyleLog::create([
            'user_id' => 1,
            'life_style_behavior_id' => $behavior['id'],
            'value' => $value,
            'total_gdf15_effect' => $gdf15Effect,
            'logged_at' => $loggedAt,
        ]);
    }

    private function randomDateBetween(Carbon $startDate, Carbon $endDate): Carbon
    {
        $randomTimestamp = mt_rand($startDate->timestamp, $endDate->timestamp);
        return Carbon::createFromTimestamp($randomTimestamp);
    }
}
