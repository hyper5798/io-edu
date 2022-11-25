<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNodeRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('node_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('node_mac');
            $table->integer('rule_order')->nullable();
            $table->string('input');
            $table->string('output');
            $table->JSON('trigger_value')->nullable();
            //$table->integer('trigger_value');
            $table->tinyInteger('operator');
            $table->integer('action')->nullable();
            $table->integer('action_value')->nullable();
            $table->integer('time')->nullable();
            $table->integer('input_type')->nullable();
            $table->integer('output_type')->nullable();
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
        Schema::dropIfExists('node_rules');
    }
}
