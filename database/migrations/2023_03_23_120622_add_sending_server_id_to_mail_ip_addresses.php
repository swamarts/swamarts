<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSendingServerIdToMailIpAddresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mail_ip_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('sending_server_id');
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
            $table->dropColumn('sending_server_id');
        });
    }
}
