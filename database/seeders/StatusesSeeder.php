<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Status::insert([
            ["name" => "Home team Winner", "created_at" => now(), "updated_at" => now()],
            ["name" => "Away team Winner", "created_at" => now(), "updated_at" => now()],
            ["name" => "Draw", "created_at" => now(), "updated_at" => now()]
        ]);
    }
}
