<?php

use App\Models\Question;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class QuestionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('questions')->truncate();
        for ($i=2 ; $i<=6 ; $i++){
            for ($j=1 ; $j<=3 ; $j++){
                //if($j==1 || $j==3) continue;
                createFake($i, $j);
            }
        }

    }
}

function createFake($fieldId, $levelId) {
    $faker = Faker::create('zh_TW');
    for ($i=1 ; $i<=50 ; $i++){
        Question::create([
            'title' => $faker->realText(10),
            'content' => $faker->realText(20),
            'option_a' => $faker->realText(10),
            'option_b' => $faker->realText(10),
            'option_c' => $faker->realText(10),
            'option_d' => $faker->realText(10),
            'option_e' => $faker->realText(10),
            'answer' => getList(),
            'field_id' => $fieldId,
            'level_id' => $levelId,
        ]);
    }
}

function getList() {
    $arr = ["a"=>"a", "b"=>"b", "c"=>"c", "d"=>"d", "e"=>"e"];
    return array_rand($arr, 2);
}
