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
        $regularLogCount = 300;
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now();

        $aprilLogCount = 50;
        $aprilStart = Carbon::create(null, 4, 1);
        $aprilEnd = Carbon::create(null, 4, 30);

        $foodIds = Food::pluck('id')->toArray();
        $gdf15Effects = [0, 5, 10];

        for ($i = 0; $i < $regularLogCount; $i++) {
            $this->createFoodLog($foodIds, $gdf15Effects, $startDate, $endDate);
        }

        for ($i = 0; $i < $aprilLogCount; $i++) {
            $this->createFoodLog($foodIds, $gdf15Effects, $aprilStart, $aprilEnd);
        }
    }

    private function createFoodLog(array $foodIds, array $gdf15Effects, Carbon $startDate, Carbon $endDate): void
    {
        FoodLog::create([
            'user_id' => 1,
            'food_id' => $foodIds[array_rand($foodIds)],
            'quantity' => rand(1, 3),
            'total_gdf15_effect' => $gdf15Effects[array_rand($gdf15Effects)],
            'taken_at' => $this->randomDateBetween($startDate, $endDate),
        ]);
    }

    private function randomDateBetween(Carbon $startDate, Carbon $endDate): Carbon
    {
        $randomTimestamp = mt_rand($startDate->timestamp, $endDate->timestamp);
        return Carbon::createFromTimestamp($randomTimestamp);
    }
}
