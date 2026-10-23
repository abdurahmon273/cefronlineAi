<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class TelegramController extends Controller
{
    private TelegramService $telegramService;
    private const COMMAND_START = '/start';

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    public function handle(Request $request): JsonResponse
    {
        Log::info('Telegram webhook keldi', $request->all());

        try {
            $update = $this->telegramService->getUpdate();
            $message = $update->getMessage();

            if (!$message) {
                return $this->jsonResponse('no_message');
            }

            $chatId = $message->getChat()->getId();

            if ($message->has('text')) {
                return $this->handleTextMessage($message->getText(), $chatId);
            }

            if ($message->has('contact')) {
                return $this->handleContactMessage($chatId, $message->getContact());
            }

            return $this->jsonResponse('unknown_command');
        } catch (Exception $e) {
            Log::error('Telegram webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->jsonResponse('error', ['message' => 'Internal server error']);
        }
    }

    private function handleTextMessage(string $text, int $chatId): JsonResponse
    {
        if ($text === self::COMMAND_START) {
            return $this->requestPhoneNumber($chatId);
        }

        if ($this->isManualPhoneNumber($text)) {
            return $this->rejectManualPhoneNumber($chatId);
        }

        return $this->jsonResponse('unknown_command');
    }

    private function handleContactMessage(int $chatId, $contact): JsonResponse
    {
        if (!$contact->getUserId()) {
            return $this->rejectManualPhoneNumber($chatId);
        }

        $phoneNumber = $this->normalizePhoneNumber($contact->getPhoneNumber());
        $user = $this->findUserByPhone($phoneNumber);

        if (!$user) {
            $this->telegramService->sendMessage($chatId, $this->getNotRegisteredMessage($phoneNumber));
            return $this->jsonResponse('contact_not_found');
        }

        try {
            $user->update(['chat_id' => $chatId]);
            $this->telegramService->sendMessage($chatId, $this->getVerificationCodeMessage($user->mobile_verify_code));
            return $this->jsonResponse('contact_verified');
        } catch (Exception $e) {
            Log::error('Failed to update user chat_id', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return $this->jsonResponse('error');
        }
    }

    private function requestPhoneNumber(int $chatId): JsonResponse
    {
        $keyboard = $this->getPhoneNumberKeyboard();
        $this->telegramService->sendMessage(
            $chatId,
            "Iltimos, telefon raqamingizni pastdagi tugma orqali yuboring 👇",
            $keyboard
        );

        return $this->jsonResponse('waiting_for_contact');
    }

    private function rejectManualPhoneNumber(int $chatId): JsonResponse
    {
        $keyboard = $this->getPhoneNumberKeyboard();
        $this->telegramService->sendMessage(
            $chatId,
            "❌ Iltimos, telefon raqamingizni pastdagi tugma orqali yuboring!",
            $keyboard
        );

        return $this->jsonResponse('invalid_contact');
    }

    private function getPhoneNumberKeyboard(): array
    {
        return [
            'keyboard' => [
                [['text' => "📞 Telefon raqamni yuborish", 'request_contact' => true]]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];
    }

    private function isManualPhoneNumber(string $text): bool
    {
        return (bool) preg_match('/^\+?\d{9,15}$/', $text);
    }

    private function normalizePhoneNumber(string $phoneNumber): string
    {
        return preg_replace('/\D/', '', $phoneNumber);
    }

    private function findUserByPhone(string $normalizedPhone): ?User
    {
        return User::whereRaw("REPLACE(mobil_number, '+', '') = ?", [$normalizedPhone])->first();
    }

    private function getVerificationCodeMessage(string $code): string
    {
        return "✅ Tasdiqlash kodingiz: " . $code;
    }

    private function getNotRegisteredMessage(string $phoneNumber): string
    {
        return "❌ Kechirasiz, siz ro'yhatdan o'tmagansiz. " . $phoneNumber;
    }

    private function jsonResponse(string $status, array $additional = []): JsonResponse
    {
        return response()->json(array_merge(['status' => $status], $additional));
    }
}
