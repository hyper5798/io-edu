<?php

use App\Models\Course;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //DB::table('courses')->truncate();
        for ($i=1 ; $i<6 ; $i++){
            $j=5;
            createFake($i, $j);
        }
    }


}

function createFake($i, $j) {
    $faker = Faker::create('zh_TW');
    Course::create([
        'category_id'=>$j,
        'title' => $faker->realText(10),
        'content'=> $i,
        'freeChapterMax' => 1,
        'isShow' => 1
    ]);
}
