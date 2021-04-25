<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductWasteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_waste', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger("product_id")->unsigned();
            $table->foreign("product_id")
                ->references('id')
                ->on('products')->onDelete('cascade');
            $table->bigInteger("waste_id")->unsigned();
            $table->foreign("waste_id")
                ->references('id')
                ->on('wastes')->onDelete('cascade');
            $table->double("quantity");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_waste');
    }
}
