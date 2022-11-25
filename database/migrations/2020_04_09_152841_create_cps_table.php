<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->nullable()->unsigned()->index(); //父公司
            $table->foreign('parent_id')->references('id')->on('cps')->onDelete('cascade');
            $table->tinyInteger('role_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('cp_name');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
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
        Schema::table('cps', function(Blueprint $table)
        {
            $table->dropForeign(['parent_id']);
        });
        Schema::dropIfExists('cps');
    }
}
