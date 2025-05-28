<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AdminSeeder::class);
        $this->call(ReadingLogSeeder::class);

        $this->call(FoodSeeder::class);
        $this->call(LifeStyleSeeder::class);
        $this->call(FoodSeeder::class);

        $this->call(LifeStyleSeeder::class);
        $this->call(FoodLogSeeder::class);
    }
}
