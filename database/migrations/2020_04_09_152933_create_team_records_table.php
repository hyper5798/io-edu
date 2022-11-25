<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('team_id');
            $table->foreign('team_id')
                ->references('id')->on('teams')
                ->onDelete('cascade');
            $table->string('team_name');
            $table->tinyInteger('room_id');
            $table->foreign('room_id')
                ->references('id')->on('rooms')
                ->onDelete('cascade');
            $table->integer('cp_id');
            $table->integer('total')->nullable();
            $table->integer('reduce')->nullable();
            $table->integer('status')->nullable();
            $table->integer('sequence')->nullable();
            $table->timestamp('start')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('end')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('team_records', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropForeign(['team_id']);
        });
        Schema::dropIfExists('team_records');
    }
}
