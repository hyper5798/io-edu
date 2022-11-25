<?php

use App\Models\Type;
use Illuminate\Database\Seeder;

class TypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rule = [
            'temperature'=>[
            '0'=>14,
            '1'=>18,
            '2'=>'data/10'
        ],
            'humidity'=>[
                '0'=>18,
                '1'=>22,
                '2'=>'data/10'
            ]];
        $json = json_encode($rule);
        $types = [[
            'id'          => 1,
            'type_id'     => 11,
            'type_name'   => '開發板',
            'description' => 'ESP-32開發板',
            'image_url'   => null,
            'rules'       => null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ],
        [
            'id'          => 2,
            'type_id'     => 99,
            'type_name'   => '溫濕度感測器',
            'description' => 'test',
            'image_url'   => null,
            'rules'       => $json,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]];

        Type::insert($types);
    }
}
