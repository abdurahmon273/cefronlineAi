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
        Schema::table('global_settings', function (Blueprint $table) {
            $table->integer('profit_tax')->nullable()->default(50);
            $table->decimal('one_writing_price',8,6)->nullable()->default(0.03);
            $table->decimal('one_speaking_price',8,6)->nullable()->default(0.03);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn('profit_tax');
            $table->dropColumn('one_writing_price');
            $table->dropColumn('one_speaking_price');
        });
    }
};
