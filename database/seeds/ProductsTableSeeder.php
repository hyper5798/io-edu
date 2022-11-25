<?php

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [[
            'id'           => 1,
            'type_id'      => 11,
            'macAddr'      => 'fcf5c4536490',
            'description'  => '公司實驗板',
            'created_at'   => now(),
            'updated_at'   => now(),
        ], [
            'id'           => 2,
            'type_id'      => 99,
            'macAddr'      => 'fcf5c4536480',
            'description'  => '密室脫逃測試',
            'created_at'   => now(),
            'updated_at'   => now(),
        ], [
            'id'           => 3,
            'type_id'      => 99,
            'macAddr'      => 'fcf5c4536481',
            'description'  => '密室脫逃測試',
            'created_at'   => now(),
            'updated_at'   => now(),
        ], [
            'id'           => 4,
            'type_id'      => 99,
            'macAddr'      => 'fcf5c4536482',
            'description'  => '密室脫逃測試',
            'created_at'   => now(),
            'updated_at'   => now(),
        ], [
            'id'           => 5,
            'type_id'      => 99,
            'macAddr'      => 'fcf5c4536483',
            'description'  => '密室脫逃測試',
            'created_at'   => now(),
            'updated_at'   => now(),
        ], [
            'id'           => 6,
            'type_id'      => 99,
            'macAddr'      => 'fcf5c4536484',
            'description'  => '密室脫逃測試',
            'created_at'   => now(),
            'updated_at'   => now(),
        ], [
            'id'           => 7,
            'type_id'      => 99,
            'macAddr'      => 'fcf5c4536485',
            'description'  => '密室脫逃測試',
            'created_at'   => now(),
            'updated_at'   => now(),
        ], [
            'id'           => 8,
            'type_id'      => 99,
            'macAddr'      => 'fcf5c4536486',
            'description'  => '密室脫逃測試',
            'created_at'   => now(),
            'updated_at'   => now(),
        ], [
            'id'           => 9,
            'type_id'      => 99,
            'macAddr'      => 'fcf5c4536487',
            'description'  => '密室脫逃測試',
            'created_at'   => now(),
            'updated_at'   => now(),
        ], [
            'id'           => 10,
            'type_id'      => 99,
            'macAddr'      => 'fcf5c4536488',
            'description'  => '密室脫逃測試',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]];

        Product::insert($datas);
    }
}
