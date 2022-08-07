<?php

namespace Database\Seeders;

use App\Models\Matches;
use Illuminate\Database\Seeder;

class MatchesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $matches = [
            [
                "time" => "2022-07-20 15:00:00", 
                "home" => 1,
                "away" => 2,
                "details" => [
                    [
                        "player_id" => 1,
                        "type" => 1,
                        "team" => "home",
                        "minute" => "20"
                    ],
                    [
                        "player_id" => 2,
                        "type" => 2,
                        "team" => "home",
                        "minute" => "25",
                        "note" => "shoot on target"
                    ],
                    [
                        "player_id" => 2,
                        "type" => 1,
                        "team" => "home",
                        "minute" => "30"
                    ]
                ]
            ],
            [
                "time" => "2022-07-24 15:00:00", 
                "home" => 1,
                "away" => 2,
                "details" => [
                    [
                        "player_id" => 1,
                        "type" => 1,
                        "team" => "home",
                        "minute" => "20"
                    ],
                    [
                        "player_id" => 2,
                        "type" => 1,
                        "team" => "home",
                        "minute" => "30"
                    ],
                    [
                        "player_id" => 2,
                        "type" => 2,
                        "team" => "home",
                        "minute" => "25",
                        "note" => "shoot on target"
                    ],
                    [
                        "player_id" => 4,
                        "type" => 1,
                        "team" => "away",
                        "minute" => "50"
                    ],
                    [
                        "player_id" => 5,
                        "type" => 1,
                        "team" => "away",
                        "minute" => "70"
                    ],
                ]
            ]
        ];

        foreach ($matches as $match) {

            $data = Matches::create([
                "time" => $match['time'], 
                "home" => $match['home'],
                "away" => $match['away'],
                "created_at" => now(), "updated_at" => now()
            ]);

            $data->details()->createMany($match['details']);

        }
    }
}
