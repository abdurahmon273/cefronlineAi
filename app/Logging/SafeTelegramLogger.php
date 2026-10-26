<?php

namespace App\Logging;

use Monolog\Handler\NullHandler;
use Monolog\Handler\TelegramBotHandler;
use Monolog\Handler\WhatFailureGroupHandler;
use Monolog\Logger;

class SafeTelegramLogger
{
    // Token yoki chat ID bo'lmasa loglar tashlab yuboriladi; Telegram xato bersa sayt to'xtamaydi.
    public function __invoke(array $config): Logger
    {
        $logger = new Logger('telegram');

        if (empty($config['token']) || empty($config['chat_id'])) {
            return $logger->pushHandler(new NullHandler());
        }

        $telegram = new TelegramBotHandler($config['token'], (string) $config['chat_id'], $config['level'] ?? 'error');

        return $logger->pushHandler(new WhatFailureGroupHandler([$telegram]));
    }
}
