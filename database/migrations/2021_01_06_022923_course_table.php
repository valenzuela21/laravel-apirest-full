<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_table', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->text('image')->nullable();
            $table->text('author');
            $table->longText('description')->nullable();
            $table->float('valor');
            $table->text('id_author')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_table');
    }
}
