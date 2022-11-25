<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupMissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_mission', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('group_id');
            $table->foreign('group_id')
                ->references('id')->on('groups')
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
        Schema::table('group_mission', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropForeign(['mission_id']);
        });
        Schema::dropIfExists('group_mission');
    }
}
