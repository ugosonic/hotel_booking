<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubCategoryAvailabilitiesTable extends Migration
{
    public function up()
    {
        Schema::create('sub_category_availabilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_category_id');
            $table->date('date');
            $table->unsignedInteger('slots')->default(1); 
            $table->boolean('is_unavailable')->default(false);

            $table->timestamps();

            $table->foreign('sub_category_id')
                  ->references('id')->on('sub_categories')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sub_category_availabilities');
    }
}
