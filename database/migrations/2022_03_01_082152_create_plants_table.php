<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('tag');
            $table->string('plant_key');
            $table->JSON('box')->nullable();//Outside location
            $table->JSON('plant')->nullable();//Inside location
            $table->tinyInteger('kind');
            $table->string('color');
            $table->string('colorBlock');
            $table->string('device_id');
            $table->foreign('device_id')
                ->references('id')->on('devices')
                ->onDelete('cascade');
            $table->timestamp('plant_time')->nullable();
            $table->timestamp('crop_time')->nullable();
            $table->timestamp('watering_time')->nullable();
            $table->timestamp('muck_time')->nullable();
            $table->tinyInteger('maturity')->nullable()->comment('預計採收日');//plant
            $table->tinyInteger('sort')->nullable()->comment('排序');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
        });
        Schema::dropIfExists('plants');
    }
}
