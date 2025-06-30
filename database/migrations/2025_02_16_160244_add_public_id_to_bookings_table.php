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
        Schema::table('bookings', function (Blueprint $table) {
            // Make sure it can hold 8 characters (or more if you like).
            // We also make it unique so two bookings never share the same code.
            $table->string('public_id', 12)->unique()->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('public_id');
        });
    }
};    