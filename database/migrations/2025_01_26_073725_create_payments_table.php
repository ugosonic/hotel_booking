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
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('booking_id');
        $table->decimal('amount', 8, 2);
        $table->string('payment_type'); // e.g. card, cash
        $table->string('status')->default('successful'); // or pending, etc.
        $table->string('reference')->nullable();
        $table->timestamps();

        // If you want a foreign key:
        $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
