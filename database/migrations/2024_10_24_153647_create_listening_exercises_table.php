<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('listening_exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('part_category');  // [1='part 1',2=>'part 2',3=>'part 3',4=>'part 4',5=>'part 5',6=>'part 6']
            $table->string('status'); // ['0'=>'free','1'=>'premium']
            $table->unsignedBigInteger('exercise_id');

            $table->foreign('exercise_id')->references('id')->on('exercises');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listening_exercises');
    }
};
