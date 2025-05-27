<?php

namespace Database\Seeders;

use App\Models\Food;
use Illuminate\Database\Seeder;
use App\Models\FoodLog;
use Carbon\Carbon;

class FoodLogSeeder extends Seeder
{
    public function run()
    {
        $startDate = Carbon::now()->startOfMonth();

        $endDate = Carbon::now();

        $logCount = 50;

        $foodIds = Food::pluck('id')->toArray();

        $gdf15Effects = [0, 5, 10];

        for ($i = 0; $i < $logCount; $i++) {
            FoodLog::create([
                'user_id' => 1,
                'food_id' => $foodIds[array_rand($foodIds)],
                'quantity' => rand(1, 3),
                'total_gdf15_effect' => $gdf15Effects[array_rand($gdf15Effects)],
                'taken_at' => $this->randomDateBetween($startDate, $endDate),
            ]);
        }
    }

    /**
     * Generate random date between two dates
     */
    private function randomDateBetween(Carbon $startDate, Carbon $endDate): Carbon
    {
        $randomTimestamp = mt_rand($startDate->timestamp, $endDate->timestamp);
        return Carbon::createFromTimestamp($randomTimestamp);
    }
}
