<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DropSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('records')->truncate();
        DB::table('tests')->truncate();
    }
}
