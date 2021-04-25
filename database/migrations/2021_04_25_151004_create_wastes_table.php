<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWastesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wastes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("reference");
            $table->bigInteger("store_id")->unsigned();
            $table->foreign("store_id")
                ->references('id')
                ->on('stores')->onDelete('cascade');
            $table->bigInteger("wastelist_id")->unsigned();
            $table->foreign("wastelist_id")
                ->references('id')
                ->on('wastelists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wastes');
    }
}
