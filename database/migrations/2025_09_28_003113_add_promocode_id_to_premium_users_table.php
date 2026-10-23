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
        Schema::table('premium_to_users', function (Blueprint $table) {

            $table->unsignedBigInteger('promocode_id')->nullable()->after('plan_id');
            $table->foreign('promocode_id')->references('id')->on('promocodes')->onDelete('cascade');

            $table->integer('discount')->default(0)->after('promocode_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('premium_to_users', function (Blueprint $table) {

            $table->dropForeign('premium_users_promocode_id_foreign');
            $table->dropColumn('promocode_id');
        });
    }
};
