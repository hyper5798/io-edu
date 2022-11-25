<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('macAddr');
            $table->tinyInteger('type_id')->nullable();
            $table->float('key1', 8,2)->nullable();
            $table->float('key2', 8,2)->nullable();
            $table->float('key3', 8,2)->nullable();
            $table->float('key4', 8,2)->nullable();
            $table->float('key5', 8,2)->nullable();
            $table->float('key6', 8,2)->nullable();
            $table->float('key7', 8,2)->nullable();
            $table->float('key8', 8,2)->nullable();
            $table->float('lat', 10,6)->nullable();
            $table->float('lng', 10,6)->nullable();
            $table->JSON('data')->nullable();
            $table->JSON('extra')->nullable();
            $table->integer('app_id')->nullable();
            $table->foreign('app_id')
                ->references('id')->on('apps')
                ->onDelete('cascade');
            $table->timestamp('recv')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['app_id']);
        });
        Schema::dropIfExists('reports');
    }
}
