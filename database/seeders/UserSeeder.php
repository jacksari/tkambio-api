<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        // $users = [];

        for ($i = 0; $i < 1000; $i++) {
            $user = [
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'birth_date' => $faker->dateTimeBetween('1980-01-01', '2010-12-31')->format('Y-m-d'),
                'email_verified_at' => now(),
                'password' => Hash::make('password'), // Puedes cambiar la contraseña por una más segura
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            DB::table('users')->insert($user);
        }

        
    }
}
