<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model
{
    protected $fillable=[
        'openai_chat_url',
        'openai_model',
        'openai_max_tokens',
        'openai_key',
        'gpt_price_per_input_token',
        'gpt_price_per_output_token',
        'deepgram_price_per_minut',
        'min_ai_credit_price',
        'usd_current_currency',
        'chat_gpt_tax',
        'profit_tax',
        'one_writing_price',
        'one_speaking_price',
        'effective_at',
        'referral_profit_percentage',
        'admin_contact',
        'start_referral_date',
        'login_bot_token',
        'cloud_vision_key',
    ];

}
