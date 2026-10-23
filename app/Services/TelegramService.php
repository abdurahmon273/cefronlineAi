<?php

namespace App\Services;

use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;
use Exception;

class TelegramService
{
    private Api $telegram;

    public function __construct()
    {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN','8033464206:AAGkH8FhwT3yY0Xl-uiQRIz4SBCkedPN5jM'));
    }

    public function getUpdate(): Update
    {
        return $this->telegram->getWebhookUpdate();
    }

    public function sendMessage(int $chatId, string $text, array $keyboard = null): void
    {
        $message = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        if ($keyboard) {
            $message['reply_markup'] = json_encode($keyboard);
        }

        $this->telegram->sendMessage($message);
    }
}
