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
            $table->unsignedBigInteger('promocode_id')->nullable();

            $table->foreign('promocode_id')->references('id')->on('promocodes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('balance_edits', function (Blueprint $table) {

            $table->dropForeign('balance_edits_promocode_id_foreign');
            $table->dropColumn('promocode_id');
        });
    }
};
