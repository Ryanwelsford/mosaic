<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStockOnHandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stock_on_hand', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger("product_id")->unsigned();
            $table->foreign("product_id")
                ->references('id')
                ->on('products')->onDelete('cascade');
            $table->bigInteger("soh_id")->unsigned();
            $table->foreign("soh_id")
                ->references('id')
                ->on('stock_on_hands')->onDelete('cascade');
            $table->double("count");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_stock_on_hand');
    }
}
