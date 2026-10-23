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
        Schema::table('writing_results', function (Blueprint $table) {
            $table->decimal('uzs_ai_check_price',12,2)->nullable()->default(0);
        });
        Schema::table('speaking_results', function (Blueprint $table) {
            $table->decimal('uzs_ai_check_price',12,2)->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('writing_results', function (Blueprint $table) {
            $table->dropColumn('uzs_ai_check_price');
        });
        Schema::table('speaking_results', function (Blueprint $table) {
            $table->dropColumn('uzs_ai_check_price');
        });
    }
};
