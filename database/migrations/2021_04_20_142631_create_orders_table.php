<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger("menu_id")->unsigned();
            $table->foreign("menu_id")
                ->references('id')
                ->on('menus')->onDelete('cascade');
            $table->string("reference")->nullable()->default(null);
            $table->string("status");
            $table->bigInteger("store_id")->unsigned();
            $table->foreign("store_id")
                ->references('id')
                ->on('stores')->onDelete('cascade');
            $table->datetime("delivery_date");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
