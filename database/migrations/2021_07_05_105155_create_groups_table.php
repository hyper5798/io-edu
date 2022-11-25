<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('cp_id');
            $table->foreign('cp_id')
                ->references('id')->on('cps')
                ->onDelete('cascade');
            $table->integer('room_id')->nullable();
            $table->foreign('room_id')
                ->references('id')->on('rooms')
                ->onDelete('cascade');
            $table->integer('mission_id')->nullable();
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
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropForeign(['mission_id']);
            $table->dropForeign(['cp_id']);
        });
        Schema::dropIfExists('groups');
    }
}
