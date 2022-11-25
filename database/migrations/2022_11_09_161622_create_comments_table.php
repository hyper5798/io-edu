<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('parent_id')->nullable()->unsigned()->index(); //父分類
            $table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade');
            $table->bigInteger('user_id');
            $table->integer('course_id');
            $table->string('specify');
            $table->string('comment');
            $table->tinyInteger('status');
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
        Schema::table('comments', function(Blueprint $table)
        {
            $table->dropForeign(['parent_id']);
        });
        Schema::dropIfExists('comments');
    }
}
