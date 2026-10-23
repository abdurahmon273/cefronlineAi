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
            $table->integer('type')->default(0);
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('package_id')->nullable();
            $table->integer('current_tax')->nullable()->default(0);

            $table->foreign('package_id')->references('id')->on('packages');
            $table->foreign('admin_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('balance_edits', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropForeign('balance_edits_admin_id_foreign');
            $table->dropColumn('admin_id');
            $table->dropForeign('balance_edits_package_id_foreign');
            $table->dropColumn('package_id');
            $table->dropColumn('current_tax');
        });
    }
};
