<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavedLessonsTable extends Migration
{
    public function up()
    {
        Schema::create('saved_lessons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('saved')->nullable();
            $table->string('liked')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
