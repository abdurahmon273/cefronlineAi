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
        Schema::table('users', function (Blueprint $table) {
            $table->string('tmp_payment_type')->nullable()->default(0);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('tmp_payment_type')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('tmp_payment_type');
            });
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn('tmp_payment_type');
            });
        });
    }
};
