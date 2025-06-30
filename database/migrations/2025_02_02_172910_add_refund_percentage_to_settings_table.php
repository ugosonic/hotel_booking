<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefundPercentageToSettingsTable extends Migration
{
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->decimal('refund_percentage', 5, 2)->default(0)->after('value');
        });
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('refund_percentage');
        });
    }
}
