<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDishPhotoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dish_photo', function (Blueprint $table) {
            $table->bigInteger('dish_id')->unsigned();
            $table->foreign('dish_id')->references('id')->on('dishs')->onDelete('cascade');
            $table->integer('photo_id');
            $table->foreign('photo_id')->references('id')->on('photo')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dish_photo');
    }
}
