<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectDishTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_dish_type', function (Blueprint $table) {
            $table->integer('project_id');
            $table->foreign('project_id')
                ->references('id')->on('projects')->onDelete('cascade');
            $table->bigInteger('dish_type_id')->unsigned();
            $table->foreign('dish_type_id')
                ->references('id')->on('dish_type')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_dish_type');
    }
}
