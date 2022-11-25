<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('room_name');
            $table->integer('pass_time');
            $table->integer('cp_id');
            $table->foreign('cp_id')
                ->references('id')->on('cps')
                ->onDelete('cascade');
            $table->bigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->string('work')->nullable()->comment('工作屬性');
            $table->string('type')->nullable()->comment('控制器類型');
            $table->tinyInteger('isSale', 1)->default(0);
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
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['cp_id']);
        });
        Schema::dropIfExists('rooms');
    }
}
