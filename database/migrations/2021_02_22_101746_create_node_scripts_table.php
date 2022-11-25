<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNodeScriptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('node_scripts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('script_name');
            $table->bigInteger('node_id');
            $table->string('node_mac');
            $table->string('api_key');
            $table->JSON('relation')->nullable();
            $table->JSON('flow')->nullable();
            $table->JSON('notify')->nullable();
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
        Schema::dropIfExists('node_scripts');
    }
}
