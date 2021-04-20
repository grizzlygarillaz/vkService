<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoPhotoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_photo', function (Blueprint $table) {
            $table->bigInteger('promo_id')->unsigned();
            $table->foreign('promo_id')
                ->references('id')
                ->on('promos')->onDelete('cascade');
            $table->bigInteger('photo_id')->unsigned();
            $table->foreign('photo_id')
                ->references('id')
                ->on('photos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promo_photo');
    }
}
