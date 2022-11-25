<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('team_id');//分隊索引
            $table->foreign('team_id')
                ->references('id')->on('teams')
                ->onDelete('cascade');
            $table->integer('room_id');//密室索引
            $table->foreign('room_id')
                ->references('id')->on('rooms')
                ->onDelete('cascade');
            $table->bigInteger('mission_id');//關卡索引
            $table->bigInteger('team_record_id');//關卡索引
            $table->timestamp('start_at')->useCurrent();//開始闖關時間
            $table->timestamp('end_at')->useCurrent();//結束闖關時間
            $table->integer('time');//關卡時間
            $table->tinyInteger('score');//關卡分數
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('records', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropForeign(['team_id']);
        });
        Schema::dropIfExists('records');
    }
}
