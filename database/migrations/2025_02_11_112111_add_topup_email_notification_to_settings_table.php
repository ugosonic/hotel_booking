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
    Schema::table('settings', function (Blueprint $table) {
        $table->boolean('topup_email_notification')->default(false);
    });
}

public function down()
{
    Schema::table('settings', function (Blueprint $table) {
        $table->dropColumn('topup_email_notification');
    });
}

};
