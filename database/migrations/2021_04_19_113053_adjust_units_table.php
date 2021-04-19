<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdjustUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('units', function (Blueprint $table) {
            $table->string("pack");
            $table->integer("quantity");
            $table->integer("pack_quantity")->nullable()->default(null);
            $table->string("pack_description")->nullable()->default(null);
            $table->dropColumn("unittype");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn("pack");
            $table->dropColumn("quantity");
            $table->dropColumn("pack_quantity");
            $table->dropColumn("pack_description");
            $table->string("unittype");
        });
    }
}
