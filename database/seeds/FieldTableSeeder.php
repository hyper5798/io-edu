<?php

use App\Constant\QuestionConstant;
use App\Models\Field;
use Illuminate\Database\Seeder;

class FieldTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fields = [[
            'title' => QuestionConstant::FIELD_1,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'title' => QuestionConstant::FIELD_2,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'title' => QuestionConstant::FIELD_3,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'title' => QuestionConstant::FIELD_4,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'title' => QuestionConstant::FIELD_5,
            'created_at' => now(),
            'updated_at' => now(),
        ],[
            'title' => QuestionConstant::FIELD_6,
            'created_at' => now(),
            'updated_at' => now(),
        ]];
        Field::insert($fields);
    }
}
