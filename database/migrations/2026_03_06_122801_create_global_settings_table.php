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
        Schema::create('global_settings', function (Blueprint $table) {
            $table->id();
            $table->text('openai_chat_url');
            $table->text('openai_model');
            $table->longText('openai_key');
            $table->integer('openai_max_tokens');
            $table->decimal('gpt_price_per_input_token', 18, 8);
            $table->decimal('gpt_price_per_output_token', 18, 8);
            $table->decimal('min_ai_credit_price',4,2);
            $table->decimal('usd_current_currency', 18, 2);
            $table->integer('chat_gpt_tax');
            $table->timestamp('effective_at')->useCurrent();
            $table->timestamps();
        });

        DB::table('global_settings')->insert([
            'openai_chat_url'=>"https://api.openai.com/v1/responses",
            'openai_model'=>"gpt-5-mini",
            'openai_max_tokens'=>7000,
            'openai_key'=>env('OPENAI_API_KEY', ''),
            'gpt_price_per_input_token' => 0.00000025,
            'gpt_price_per_output_token' => 0.000002,
            'min_ai_credit_price'=>0.9,
            'usd_current_currency'=>123000,
            'chat_gpt_tax'=>12,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_settings');
    }
};
