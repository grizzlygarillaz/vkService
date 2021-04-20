<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->dateTime('publish_date');
            $table->longText('text');
            $table->integer('project');
            $table->foreign('project')->references('id')->on('project');
            $table->bigInteger('post_type')->unsigned();
            $table->foreign('post_type')->references('id')->on('post_type');
            $table->integer('dish_type')->nullable();
            $table->integer('promo_id')->nullable();
            $table->foreign('promo_id')->references('id')->on('promo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
