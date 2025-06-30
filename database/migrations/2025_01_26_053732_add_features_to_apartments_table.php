<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->boolean('has_rooms')->default(false);
            $table->unsignedInteger('num_rooms')->nullable();
            $table->boolean('has_toilets')->default(false);
            $table->unsignedInteger('num_toilets')->nullable();
            $table->boolean('has_sittingroom')->default(false);
            $table->unsignedInteger('num_sittingrooms')->nullable();
            $table->boolean('has_kitchen')->default(false);
            $table->unsignedInteger('num_kitchens')->nullable();
            $table->boolean('has_balcony')->default(false);
            $table->unsignedInteger('num_balconies')->nullable();
            $table->boolean('free_wifi')->default(false);
            $table->boolean('water')->default(false);
            $table->boolean('electricity')->default(false);
            $table->text('additional_info')->nullable();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            //
        });
    }
};
