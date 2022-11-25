<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('device_id')->nullable();
            $table->foreign('device_id')
                ->references('id')->on('devices')
                ->onDelete('cascade');
            $table->tinyInteger('type_id')->nullable();
            $table->bigInteger('app_id')->nullable();
            $table->foreign('app_id')
                ->references('id')->on('apps')
                ->onDelete('cascade');
            $table->bigInteger('user_id')->nullable();
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->integer('cp_id')->nullable();
            $table->foreign('cp_id')
                ->references('id')->on('cps')
                ->onDelete('cascade');
            $table->integer('room_id')->nullable();
            $table->string('field');
            $table->JSON('set');
            $table->tinyInteger('set_index')->nullable()->comment('current index');
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
        Schema::table('settings', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
            $table->dropForeign(['app_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['cp_id']);
        });
        Schema::dropIfExists('settings');
    }
}
