<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_product', function (Blueprint $table) {
            $table->id();

            $table->bigInteger("menu_id")->unsigned();
            $table->foreign("menu_id")
                ->references('id')
                ->on('menus')->onDelete('cascade');

            $table->bigInteger("product_id")->unsigned();
            $table->foreign("product_id")
                ->references('id')
                ->on("products")->onDelete("cascade");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_product');
    }
}
