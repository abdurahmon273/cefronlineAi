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
        Schema::table('full_results', function (Blueprint $table) {
            $table->timestamp('date')->nullable()->useCurrent();
        });

        DB::table('full_results')->update(['date' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('full_results', function (Blueprint $table) {
            $table->dropColumn('date');
        });
    }
};
