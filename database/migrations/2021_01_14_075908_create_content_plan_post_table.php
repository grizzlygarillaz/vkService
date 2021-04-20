<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentPlanPostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_plan_post', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('content_plan')->unsigned();
            $table->foreign('content_plan')->references('id')->on('content_plan')->onDelete('cascade');
            $table->dateTime('publish_date');
            $table->text('text');
            $table->bigInteger('post_type')->unsigned();
            $table->foreign('post_type')->references('id')->on('post_type')->onDelete('cascade');
            $table->bigInteger('type_select')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_plan_post');
    }
}
