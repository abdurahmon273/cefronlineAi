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
        Schema::table('reading_exercises', function (Blueprint $table) {
            $table->bigInteger('time_limit')->nullable();
            $table->string('difficulty')->default('1'); // [0,1,2]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reading_exercises', function (Blueprint $table) {
             $table->dropColumn('time_limit');
             $table->dropColumn('difficulty');
        });
    }
};
