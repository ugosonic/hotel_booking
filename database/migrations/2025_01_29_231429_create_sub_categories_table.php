<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('category_id')
                  ->references('id')->on('categories')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sub_categories');
    }
}
