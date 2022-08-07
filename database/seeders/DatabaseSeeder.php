<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(1)->create();
        $this->call(CitiesSeeder::class);
        $this->call(StatusesSeeder::class);
        $this->call(PositionsSeeder::class);
        $this->call(TeamsSeeder::class);
        $this->call(PlayersSeeder::class);
        $this->call(MatchesSeeder::class);
        
    }
}
