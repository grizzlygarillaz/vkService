<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDishsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dishs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name', 55);
            $table->string('site_link')->nullable();
            $table->integer('image')->nullable();
            $table->foreign('image')->references('id')->on('photo')->onDelete('set null');
            $table->text('ingredients')->nullable();
            $table->string('weight', 55)->nullable();
            $table->string('old_price', 55)->nullable();
            $table->string('new_price', 55)->nullable();
            $table->bigInteger('type_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dishs');
    }
}
