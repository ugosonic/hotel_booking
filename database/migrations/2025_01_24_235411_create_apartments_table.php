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
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->string('apartment_name');
            $table->decimal('price', 8, 2)->nullable();
            // add any other fields your app needs
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
