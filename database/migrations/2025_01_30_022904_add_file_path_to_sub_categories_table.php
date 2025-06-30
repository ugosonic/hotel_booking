<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFilePathToSubCategoriesTable extends Migration
{
    public function up()
    {
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('price');
            // or wherever you want the column
        });
    }

    public function down()
    {
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->dropColumn('file_path');
        });
    }
}
