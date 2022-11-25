<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commands', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('sequence');
            $table->tinyInteger('type_id');
            $table->bigInteger('device_id');
            $table->foreign('device_id')
                ->references('id')->on('devices')
                ->onDelete('cascade');
            $table->string('cmd_name',10);
            $table->string('command',30);
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
        Schema::table('commands', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
        });
        Schema::dropIfExists('commands');
    }
}
