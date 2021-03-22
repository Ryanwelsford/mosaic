<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer("number");
            $table->string("name");
            $table->string("address1");
            $table->string("address2")->nullable();
            $table->string("address3")->nullable();
            $table->string("postcode");
            $table->bigInteger("user_id")->unsigned();
            $table->foreign("user_id")
                ->references('id')
                ->on("users")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
}
