<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScriptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scripts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('script_name')->comment('mission scrip');
            $table->bigInteger('mission_id');
            $table->foreign('mission_id')
                ->references('id')->on('missions')
                ->onDelete('cascade');
            $table->bigInteger('room_id');
            $table->string('content');
            $table->string('prompt1')->nullable();
            $table->string('prompt2')->nullable();
            $table->string('prompt3')->nullable();
            $table->JSON('pass')->nullable();
            $table->string('next_pass')->nullable();
            $table->integer('next_sequence')->nullable();
            $table->string('note')->nullable();
            $table->string('image_url')->nullable();
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
        Schema::table('scripts', function (Blueprint $table) {
            $table->dropForeign(['mission_id']);
        });
        Schema::dropIfExists('scripts');
    }
}
