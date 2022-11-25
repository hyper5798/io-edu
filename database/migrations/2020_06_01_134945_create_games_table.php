<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('game_name');
            $table->tinyInteger('room_id');
            $table->foreign('room_id')
                ->references('id')->on('rooms')
                ->onDelete('cascade');
            $table->bigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->bigInteger('cp_id');
            $table->foreign('cp_id')
                ->references('id')->on('cps')
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
        Schema::table('games', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['cp_id']);
        });
        Schema::dropIfExists('games');
    }
}
