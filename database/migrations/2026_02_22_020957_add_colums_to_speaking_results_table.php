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
        Schema::table('speaking_results', function (Blueprint $table) {
            $table->integer('with_ai')->default(0);
            $table->decimal('score', 8, 2)->default(0);
            $table->integer('max_score')->default(0);
            $table->decimal('ai_check_price',8,6)->default(0);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('speaking_results', function (Blueprint $table) {
            $table->dropColumn('with_ai');
            $table->dropColumn('score');
            $table->dropColumn('max_score');
            $table->dropColumn('ai_check_price');

        });
    }
};
