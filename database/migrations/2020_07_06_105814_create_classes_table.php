<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('class_name');
            $table->integer('cp_id');
            $table->foreign('cp_id')
                ->references('id')->on('cps')
                ->onDelete('cascade');
            $table->bigInteger('user_id');
            $table->tinyInteger('class_option');
            $table->JSON('members')->nullable();
            $table->JSON('devices')->nullable();
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
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['cp_id']);
        });
        Schema::dropIfExists('classes');
    }
}
