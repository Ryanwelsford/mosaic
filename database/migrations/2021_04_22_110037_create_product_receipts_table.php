<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_receipt', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger("receipt_id")->unsigned();
            $table->foreign("receipt_id")
                ->references('id')
                ->on('receipts')->onDelete('cascade');
            $table->bigInteger("product_id")->unsigned();
            $table->foreign("product_id")
                ->references('id')
                ->on('products')->onDelete('cascade');
            $table->integer("quantity");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_receipt');
    }
}
