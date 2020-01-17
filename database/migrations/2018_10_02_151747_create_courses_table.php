<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');         
            $table->timestamps();
            $table->string("name");
            $table->integer("numholes");
            $table->string("address")->nullable();
            $table->string("city")->nullable();
            $table->integer("zipcode")->nullable();
            $table->text('description')->nullable();
            $table->decimal('lat',10,8);
            $table->decimal('lng',11,8);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
