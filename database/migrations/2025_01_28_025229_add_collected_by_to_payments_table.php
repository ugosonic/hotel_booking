<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('payments', function (Blueprint $table) {
        $table->unsignedBigInteger('collected_by')->nullable()->after('payment_method'); // Add collected_by column
    });
}

public function down()
{
    Schema::table('payments', function (Blueprint $table) {
        $table->dropColumn('collected_by');
    });
}

};
