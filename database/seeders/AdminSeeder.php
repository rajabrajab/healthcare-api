<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;


class AdminSeeder extends Seeder
{
    public function run(): void
    {

        $adminUsersData =
            [
                'email'     => 'admin@gmail.com',
                'name' => 'admin',
                'password'  => Hash::make('password')
            ];

        User::firstOrCreate($adminUsersData);

        $adminUsersData2 =
            [
                'email'     => 'admin2@gmail.com',
                'name' => 'admin2',
                'password'  => Hash::make('password')
            ];

        User::firstOrCreate($adminUsersData2);
    }
}
