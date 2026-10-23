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
        Schema::create('balance_edits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('price', 15, 4)->default(0.0000);
            $table->decimal('value', 15, 2)->default(0.00);
            $table->decimal('current_currency', 15, 2)->default(0.00);
            $table->integer('discount')->default(0);
            $table->decimal('current_balance', 15, 4)->default(0.0000);

            $table->foreign('user_id')->references('id')->on('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_edits');
    }
};
