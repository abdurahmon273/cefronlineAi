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
        Schema::table('balance_edits', function (Blueprint $table) {
            $table->decimal('price',15,2)->default(0)->change();
            $table->decimal('value',15,6)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('balance_edits', function (Blueprint $table) {
            $table->decimal('price', 15, 6)->default(0.0000)->change();
            $table->decimal('value', 15, 2)->default(0.0000)->change();
        });
    }
};
