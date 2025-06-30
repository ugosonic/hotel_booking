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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id')->nullable(); // if staff made the booking
            $table->unsignedBigInteger('client_id')->nullable(); // if booked for a client
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_address')->nullable();
            $table->string('guest_phone')->nullable();
            $table->date('guest_dob')->nullable();
        
            $table->string('doc_type')->nullable(); // 'licence','passport','nin' 
            $table->string('doc_number')->nullable();
            $table->string('doc_upload')->nullable(); // path to uploaded doc
        
            $table->unsignedBigInteger('apartment_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('nights')->default(1);
            
            $table->enum('status', ['pending','successful','canceled'])->default('pending');
            $table->timestamps();
            // ...
        });
    }        
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
