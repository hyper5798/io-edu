<?php
use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $room = [[
            'id'         => 1,
            'room_name' => '光引擎',
            'pass_time' => 3000,
            'cp_id'      => 1,
            'user_id'    => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]];

        Room::insert($room);
    }
}
