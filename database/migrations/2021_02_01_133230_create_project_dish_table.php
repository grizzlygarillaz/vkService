<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectDishTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_dish', function (Blueprint $table) {
            $table->integer('project_id');
            $table->foreign('project_id')
                ->references('id')->on('projects')->onDelete('cascade');
            $table->bigInteger('dishs_id')->unsigned();
            $table->foreign('dishs_id')
                ->references('id')->on('dishs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_dish');
    }
}
