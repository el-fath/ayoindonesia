<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Position::insert([
            ["name" => "goal keeper", "created_at" => now(), "updated_at" => now()],
            ["name" => "defender", "created_at" => now(), "updated_at" => now()],
            ["name" => "mid fielder", "created_at" => now(), "updated_at" => now()],
            ["name" => "striker", "created_at" => now(), "updated_at" => now()]
        ]);
    }
}
