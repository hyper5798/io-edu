<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceMissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_mission', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('device_id');
            $table->foreign('device_id')
                ->references('id')->on('devices')
                ->onDelete('cascade');
            $table->integer('mission_id');
            $table->foreign('mission_id')
                ->references('id')->on('missions')
                ->onDelete('cascade');
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
        Schema::table('device_mission', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
            $table->dropForeign(['mission_id']);
        });
        Schema::dropIfExists('device_mission');
    }
}
