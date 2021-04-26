<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_product', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger("product_id")->unsigned();
            $table->foreign("product_id")
                ->references('id')
                ->on('products')->onDelete('cascade');
            $table->bigInteger("inventory_id")->unsigned();
            $table->foreign("inventory_id")
                ->references('id')
                ->on('inventories')->onDelete('cascade');
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
        Schema::dropIfExists('inventory_product');
    }
}
