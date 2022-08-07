<?php

namespace Database\Seeders;

use App\Models\Player;
use Illuminate\Database\Seeder;

class PlayersSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $liverpool = [
            [
                "name" => "Moh Salah", 
                "height" => "170",
                "weight" => "70",
                "position" => [3,4],
                "number" => 10,
                "team_id" => 1
            ],
            [
                "name" => "Sadio Mane", 
                "height" => "170",
                "weight" => "70",
                "position" => [2,3,4],
                "number" => 7,
                "team_id" => 1
            ],
            [
                "name" => "alisson", 
                "height" => "170",
                "weight" => "70",
                "position" => [1,2],
                "number" => 1,
                "team_id" => 1
            ]
        ];

        $man_united = [
            [
                "name" => "Ronaldo", 
                "height" => "170",
                "weight" => "70",
                "position" => [3,4],
                "number" => 10,
                "team_id" => 2
            ],
            [
                "name" => "Lingard", 
                "height" => "170",
                "weight" => "70",
                "position" => [2,3,4],
                "number" => 7,
                "team_id" => 2
            ],
            [
                "name" => "De Gea", 
                "height" => "170",
                "weight" => "70",
                "position" => [1,2],
                "number" => 1,
                "team_id" => 2
            ]
        ];

        $man_city = [
            [
                "name" => "Sterling", 
                "height" => "170",
                "weight" => "70",
                "position" => [3,4],
                "number" => 10,
                "team_id" => 3
            ],
            [
                "name" => "Aguero", 
                "height" => "170",
                "weight" => "70",
                "position" => [2,3,4],
                "number" => 7,
                "team_id" => 3
            ],
            [
                "name" => "Bravo", 
                "height" => "170",
                "weight" => "70",
                "position" => [1,2],
                "number" => 1,
                "team_id" => 3
            ]
        ];

        $players = array_merge($liverpool, $man_united, $man_city);

        foreach ($players as $player) {

            $data = Player::create([
                "name"    => $player['name'], 
                "height"  => $player['height'],
                "weight"  => $player['weight'],
                "number"  => $player['number'],
                "team_id" => $player['team_id']
            ]);

            $data->positions()->sync($player['position']);
        }
    }
}
