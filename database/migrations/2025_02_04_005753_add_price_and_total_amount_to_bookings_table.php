<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceAndTotalAmountToBookingsTable extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->after('nights');
            $table->decimal('total_amount', 10, 2)->default(0)->after('price');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('total_amount');
        });
    }
}

