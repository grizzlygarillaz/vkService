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
            $table->bigInteger('post_id')->unsigned();
            $table->foreign('post_id')
                ->references('id')
                ->on('posts')->onDelete('cascade');
            $table->bigInteger('content_plan_id')->unsigned();
            $table->foreign('content_plan_id')
                ->references('id')
                ->on('content_plan')->onDelete('cascade');
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
