<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientNameToBookingsTable extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('client_name')->nullable()->after('client_id');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('client_name');
        });
    }
}
