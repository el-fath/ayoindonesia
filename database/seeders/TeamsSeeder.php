<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Team::insert([
            [
                "name" => "Liverpool", 
                "logo" => "liverpool.jpg",
                "since" => "1975-08-03",
                "address" => "team address",
                "city_id" => 1,
                "created_at" => now(), "updated_at" => now()
            ],
            [
                "name" => "Manchaster United", 
                "logo" => "manchaster-united.jpg",
                "since" => "1975-08-03",
                "address" => "team address",
                "city_id" => 2,
                "created_at" => now(), "updated_at" => now()
            ],
            [
                "name" => "Manchaster City", 
                "logo" => "manchaster-city.jpg",
                "since" => "1975-08-03",
                "address" => "team address",
                "city_id" => 2,
                "created_at" => now(), "updated_at" => now()
            ]
        ]);
    }
}
