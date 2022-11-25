<?php

use Illuminate\Database\Seeder;
use App\Models\Game;

class GamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $game = [[
            'id'         => 1,
            'game_name'  => 'game1',
            'room_id'    => 1,
            'user_id'    => 1,
            'cp_id'      => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'id'         => 2,
            'game_name'  => 'game2',
            'room_id'    => 1,
            'user_id'    => 1,
            'cp_id'      => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
        'id'         => 3,
            'game_name'  => 'game3',
            'room_id'    => 1,
            'user_id'    => 1,
            'cp_id'      => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]];

        Game::insert($game);
    }
}
