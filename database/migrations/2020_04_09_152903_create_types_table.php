<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('type_id');
            $table->tinyInteger('category');
            $table->string('type_name');
            $table->string('description')->nullable();
            $table->string('image_url')->nullable();
            $table->JSON('fields')->nullable();
            $table->JSON('rules')->nullable();
            $table->string('work', 20)->nullable();
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
        Schema::dropIfExists('types');
    }
}
