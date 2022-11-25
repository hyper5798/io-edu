<?php
use App\Models\Command;
use Illuminate\Database\Seeder;

class CommandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [[
            'id'         => 1,
            'type_id'    => 21,
            'device_id'  => null,
            "cmd_name"   => 'open_light',
            "command"    => 'fc000102030405060708',
            'created_at' => now(),
            'updated_at' => now(),
        ]];
        Command::insert($data);
    }
}
