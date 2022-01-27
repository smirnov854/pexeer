<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOfferRateDecimalValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('buys', function (Blueprint $table) {
            $table->decimal('coin_rate', 19, 8)->default(0)->change();
            $table->decimal('rate_percentage', 19, 8)->default(0)->change();
            $table->decimal('market_price', 19, 8)->default(0)->change();
        });
        Schema::table('sells', function (Blueprint $table) {
            $table->decimal('coin_rate', 19, 8)->default(0)->change();
            $table->decimal('rate_percentage', 19, 8)->default(0)->change();
            $table->decimal('market_price', 19, 8)->default(0)->change();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('rate',13,8)->default(0)->change();
            $table->decimal('amount',13,8)->default(0)->change();
            $table->decimal('price',13,8)->default(0)->change();
            $table->decimal('fees',13,8)->default(0)->change();
            $table->decimal('fees_percentage',13,8)->default(0)->change();
        });
        Schema::table('escrows', function (Blueprint $table) {
            $table->decimal('amount',13,8)->default(0)->change();
            $table->decimal('fees',13,8)->default(0)->change();
            $table->decimal('fees_percentage',13,8)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
