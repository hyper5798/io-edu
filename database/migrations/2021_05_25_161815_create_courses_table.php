<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('image_url')->comment(' 圖片位址');
            $table->tinyInteger('category_id')->comment('分類ID');
            $table->string('title')->comment('標題');
            $table->string('content_small')->comment('簡介');
            $table->longText('content');
            $table->tinyInteger('freeChapterMax')->comment('最大免費單元');
            $table->tinyInteger('is_show')->default(1)->comment('0備課,1上架');
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
        Schema::dropIfExists('courses');
    }
}
