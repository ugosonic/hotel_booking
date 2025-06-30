<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserActivitiesTable extends Migration
{
    public function up()
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type')->nullable(); // e.g. "login", "payment", "booking"
            $table->string('description', 500); // short text describing the action
            $table->timestamps();               // created_at, updated_at

            $table->foreign('user_id')->references('id')->on('users')
                  ->onDelete('cascade'); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_activities');
    }
}
