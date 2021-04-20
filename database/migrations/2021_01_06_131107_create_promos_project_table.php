<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromosProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_project', function (Blueprint $table) {
            $table->integer('promo_id');
            $table->foreign('promo_id')
                ->references('id')
                ->on('promo')->onDelete('cascade');
            $table->integer('project_id');
            $table->foreign('project_id')
                ->references('id')
                ->on('project')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promos_project');
    }
}
