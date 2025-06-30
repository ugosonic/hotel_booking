<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubCategoryImagesTable extends Migration
{
    public function up()
    {
        Schema::create('sub_category_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_category_id');
            $table->string('image_path'); // e.g. "uploads/category_2/subcat_5/imageXYZ.jpg"
            $table->timestamps();

            $table->foreign('sub_category_id')
                  ->references('id')->on('sub_categories')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sub_category_images');
    }
}
