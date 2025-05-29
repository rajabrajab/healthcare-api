<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReadingLog;
use Illuminate\Support\Carbon;

class ReadingLogSeeder extends Seeder
{
    public function run()
    {

        $date = Carbon::now()->startOfMonth();

        foreach (range(1, 350) as $i) {
            $readingDate = $date->copy()->addDays(rand(0, 25));
            $readingTime = Carbon::createFromTime(rand(6, 20), 0, 0)->format('H:i:s');

            ReadingLog::create([
                'user_id' => 1,
                'reading_date' => $readingDate->toDateString(),
                'reading_time' => $readingTime,
                'eaze_diabetes' => 'eaze diabetes',
                'drug_response' => 'drug response',
                'reading' => rand(80, 160),
            ]);
        }
    }
}
