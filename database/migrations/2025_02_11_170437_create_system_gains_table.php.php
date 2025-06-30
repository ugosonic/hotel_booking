<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemGainsTable extends Migration
{
    public function up()
    {
        Schema::create('system_gains', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type')->default('refund');
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();

            // If you want foreign keys:
            // $table->foreign('booking_id')->references('id')->on('bookings');
            // $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_gains');
    }
}
