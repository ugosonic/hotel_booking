<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->boolean('login_notification')->default(true);
            $table->boolean('password_changed_notification')->default(true);
            $table->boolean('payment_error_notification')->default(true);
            $table->boolean('payment_success_notification')->default(true);
            $table->boolean('pending_topup_notification')->default(true);
            $table->boolean('registration_welcome_notification')->default(true);
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_settings');
    }
};
