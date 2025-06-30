<?php

// database/migrations/2023_02_04_000000_update_key_column_in_settings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateKeyColumnInSettingsTable extends Migration
{
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('key')->nullable()->change();
            // or ->default('refund_percentage')->change();
        });
    }

    public function down()
    {
        // If originally was NOT NULL, revert:
        Schema::table('settings', function (Blueprint $table) {
            $table->string('key')->nullable(false)->change();
        });
    }
}
