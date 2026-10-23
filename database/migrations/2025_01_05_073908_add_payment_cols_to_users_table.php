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
            $table->string('tmp_payment_slug')->nullable();
            $table->string('tmp_payment_price')->nullable();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('tmp_payment_slug')->nullable();
            $table->string('tmp_payment_price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tmp_payment_slug');
            $table->dropColumn('tmp_payment_price');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('tmp_payment_slug');
            $table->dropColumn('tmp_payment_price');
        });
    }
};
