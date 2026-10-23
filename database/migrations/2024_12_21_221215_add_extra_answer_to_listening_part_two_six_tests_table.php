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
        Schema::table('listening_part_two_six_tests', function (Blueprint $table) {
            $table->string('extra_answer')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listening_part_two_six_tests', function (Blueprint $table) {
            //
            $table->dropColumn('extra_answer');
        });
    }
};
