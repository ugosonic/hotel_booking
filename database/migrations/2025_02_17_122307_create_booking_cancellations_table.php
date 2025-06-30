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
    Schema::create('booking_cancellations', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('booking_id');
        $table->unsignedBigInteger('user_id')->nullable(); // user who the cancellation is for
        $table->unsignedBigInteger('canceled_by')->nullable(); // staff or client who triggered
        $table->decimal('canceled_amount', 10,2)->default(0.00); // total_amount of booking
        $table->decimal('refunded_amount', 10,2)->default(0.00);
        $table->decimal('system_gain', 10,2)->default(0.00);
        $table->timestamp('canceled_at')->useCurrent();
        $table->timestamps();
        // foreign keys if you want
     $table->foreign('booking_id')->references('id')->on('bookings');
        // ...
    });
}

public function down()
{
    Schema::dropIfExists('booking_cancellations');
}
};