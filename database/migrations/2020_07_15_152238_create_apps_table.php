<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->bigInteger('device_id');
            $table->foreign('device_id')
                ->references('id')->on('devices')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->string('macAddr');
            $table->string('api_key')->nullable();
            $table->JSON('key_label');
            $table->JSON('key_parse')->nullable();
            $table->tinyInteger('sequence')->nullable();
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
        Schema::table('apps', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
        });
        Schema::dropIfExists('apps');
    }
}
