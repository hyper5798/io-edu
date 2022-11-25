<?php

/** @var Factory $factory */

use App\Models\Report;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Report::class, function (Faker $faker) {
    $arr = [
        'temperature'=> 29.9,
        'humidity'=> 47,
    ];
    $json = json_encode($arr);
    return [
        'mac'=> '0000000005010b7d',
        'type_id' => 1,
        'data'       => $json,
        'extra'      => null,
        'recv'       => $faker->dateTime(),
    ];
});
