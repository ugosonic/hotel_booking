<?php

// database/migrations/2023_09_01_000000_add_pending_expires_at_to_bookings.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPendingExpiresAtToBookings extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dateTime('pending_expires_at')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('pending_expires_at');
        });
    }
}

