<?php

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('course_categories')->truncate();
        for ($i=1 ; $i<=6 ; $i++){
            createFake($i);
        }
    }
}

function createFake($i) {
    $faker = Faker::create('zh_TW');
    CourseCategory::create([
        'title' => $faker->realText(10)
    ]);
}
