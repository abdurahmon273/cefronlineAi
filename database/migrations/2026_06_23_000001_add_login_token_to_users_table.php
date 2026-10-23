<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('login_token')->nullable()->index()->after('session_id');
            $table->timestamp('login_token_expires_at')->nullable()->after('login_token');
            $table->timestamp('login_token_used_at')->nullable()->after('login_token_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['login_token']);
            $table->dropColumn(['login_token', 'login_token_expires_at', 'login_token_used_at']);
        });
    }
};
