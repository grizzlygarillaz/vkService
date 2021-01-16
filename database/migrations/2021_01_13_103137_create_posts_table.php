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
            $table->bigInteger('post_type')->unsigned();
            $table->foreign('post_type')->references('id')->on('post_type')->onDelete('cascade');
            $table->integer('dish_type')->nullable();
            $table->integer('promo_id')->nullable()->references('id')->on('promo')->onDelete('cascade');
            $table->tinyInteger('available')->default(0);
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
