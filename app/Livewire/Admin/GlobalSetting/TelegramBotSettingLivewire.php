<?php

namespace App\Livewire\Admin\GlobalSetting;

use App\Models\GlobalSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Throwable;

class TelegramBotSettingLivewire extends Component
{
    public $login_bot_token;
    public $globalSetting;

    public function mount()
    {
        $this->globalSetting = GlobalSetting::orderByDesc('effective_at')->select('login_bot_token')->firstOrFail();
        $this->login_bot_token = $this->globalSetting->login_bot_token;
    }

    public function saveLoginBot()
    {
        $this->validate(['login_bot_token' => 'required']);

        $token = trim($this->login_bot_token);

        $this->globalSetting->update([
            'login_bot_token' => $token,
        ]);

        $webhookUrl = route('botwebhook.confirm');

        try {
            Http::retry(2, 1000, throw: false)
                ->timeout(15)
                ->post("https://api.telegram.org/bot{$token}/deleteWebhook");
        } catch (Throwable $e) {
            Log::warning('Telegram deleteWebhook failed', [
                'message' => $e->getMessage(),
            ]);
        }

        try {
            $webhookResponse = Http::retry(2, 1000, throw: false)
                ->timeout(20)
                ->post("https://api.telegram.org/bot{$token}/setWebhook", [
                    'url' => $webhookUrl,
                ]);
        } catch (Throwable $e) {
            $this->dispatch('global_alert', 0, "Telegram bilan ulanishda xatolik yuz berdi: {$e->getMessage()}");
            return;
        }

        if ($webhookResponse->successful() && $webhookResponse->json('ok')) {
            $this->dispatch('global_alert', 1, 'Telegram Bot Tokeni saqlandi va webhook sozlandi.');
            return;
        }

        $message = $webhookResponse->json('description') ?? 'unknown error';
        $this->dispatch('global_alert', 0, "Telegram Bot Tokeni saqlandi, lekin webhookni sozlashda xatolik yuz berdi: {$message}");
    }

    public function render()
    {
        return view('livewire.admin.global-setting.telegram-bot-setting-livewire');
    }
}
