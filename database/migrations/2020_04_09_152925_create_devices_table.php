<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('device_name');
            $table->string('macAddr');
            $table->integer('status');
            $table->integer('cp_id')->references('id')->on('cps');
            $table->bigInteger('user_id');
            $table->tinyInteger('type_id');
            $table->tinyInteger('product_id');
            $table->foreign('product_id')
                ->references('id')->on('products')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->tinyInteger('network_id');
            $table->integer('setting_id')->nullable();
            $table->boolean('make_command')->default(0);
            $table->string('description')->nullable();
            $table->string('image_url')->nullable();
            $table->tinyInteger('isPublic')->default(0);
            $table->tinyInteger('support')->default(0)->comment('0:none, 1:UAV');
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
        Schema::table('devices', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['product_id']);
        });
        Schema::dropIfExists('devices');
    }
}
