<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceMonthlyAndYearlyToMailIpAddresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mail_ip_addresses', function (Blueprint $table) {
            $table->float('price_monthly', 8,2);
            $table->float('price_yearly', 8,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mail_ip_addresses', function (Blueprint $table) {
            $table->dropColumn('price_monthly');
            $table->dropColumn('price_yearly');
        });
    }
}
