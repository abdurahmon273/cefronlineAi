<?php

namespace App\Http\Controllers;

use App\Models\GlobalSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Throwable;

class LoginBotController extends Controller
{
    private const COMMAND_START = '/start';

    public function redirectToBot(): RedirectResponse
    {
        $token = $this->getLoginBotToken();

        Log::info('LoginBotController redirectToBot started', [
            'has_token' => filled($token),
        ]);

        if (!$token) {
            Log::warning('LoginBotController redirectToBot missing token');
            return redirect()->route('login')->withErrors([
                'mobile_number_or_email' => 'Telegram login bot token sozlanmagan.',
            ]);
        }

        try {
            $response = Http::retry(2, 1000, throw: false)
                ->timeout(15)
                ->get("https://api.telegram.org/bot{$token}/getMe");
        } catch (Throwable $e) {
            Log::warning('Telegram getMe failed', [
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('login')->withErrors([
                'mobile_number_or_email' => 'Telegram bot bilan ulanishda xatolik yuz berdi.',
            ]);
        }

        $username = $response->json('result.username');

        Log::info('LoginBotController redirectToBot getMe response', [
            'status' => $response->status(),
            'ok' => $response->json('ok'),
            'username' => $username,
            'body' => $response->json() ?? $response->body(),
        ]);

        if (!$response->successful() || !$response->json('ok') || !$username) {
            Log::warning('LoginBotController redirectToBot invalid getMe response');
            return redirect()->route('login')->withErrors([
                'mobile_number_or_email' => 'Telegram bot username olinmadi.',
            ]);
        }

        return redirect()->away("https://t.me/{$username}?start=login");
    }

    public function handle(Request $request): JsonResponse
    {
        try {
            $globalSetting = GlobalSetting::orderByDesc('effective_at')->firstOrFail();
            $token = trim($globalSetting->login_bot_token);

            Log::info('LoginBotController webhook hit', [
                'token_prefix' => substr($token, 0, 12),
                'payload' => $request->all(),
            ]);

            $message = $request->input('message');

            if (!$message) {
                Log::warning('LoginBotController webhook no message', [
                    'payload' => $request->all(),
                ]);
                return $this->jsonResponse('no_message');
            }

            $chatId = data_get($message, 'chat.id');
            if (!$chatId) {
                Log::warning('LoginBotController webhook missing chat id', [
                    'message' => $message,
                ]);
                return $this->jsonResponse('missing_chat_id');
            }

            $text = data_get($message, 'text');
            if (is_string($text)) {
                Log::info('LoginBotController webhook text message', [
                    'chat_id' => $chatId,
                    'text' => $text,
                ]);
                return $this->handleTextMessage($token, (int) $chatId, $text);
            }

            $contact = data_get($message, 'contact');
            if (is_array($contact)) {
                Log::info('LoginBotController webhook contact message', [
                    'chat_id' => $chatId,
                    'contact' => $contact,
                ]);
                return $this->handleContactMessage($token, (int) $chatId, $message, $contact);
            }

            Log::warning('LoginBotController webhook unknown payload type', [
                'message' => $message,
            ]);
            return $this->jsonResponse('unknown_command');
        } catch (Throwable $e) {
            Log::error('Telegram login bot webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->jsonResponse('error', ['message' => 'Internal server error']);
        }
    }

    public function callback(Request $request): RedirectResponse
    {
        Log::info('LoginBotController callback hit', [
            'query' => $request->query(),
        ]);

            if (!$request->hasValidSignature()) {
                Log::warning('LoginBotController callback invalid signature', [
                    'query' => $request->query(),
                ]);
                abort(403);
            }

        $validated = $request->validate([
            'uniq_token' => ['required'],
        ]);


        $user = $this->findUserByToken($validated['uniq_token']);

        Log::info('LoginBotController callback token lookup', [
            'uniq_token' => $validated['uniq_token'],
            'user_found' => (bool) $user,
            'user_id' => $user?->id,
        ]);

        if (!$user) {
            Log::warning('LoginBotController callback token not found', [
                'uniq_token' => $validated['uniq_token'],
            ]);
            return redirect()->route('login')->withErrors([
                'mobile_number_or_email' => "Telegram login expired, please try again.",
            ]);
        }

        $stateType = $request->query('state_type', 'web');

        if ($stateType === 'mobile') {
            // --- MOBILE: bir martalik qisqa muddatli login_token yaratib,
            //     universal link orqali ilovaga qaytaramiz. Haqiqiy auth token
            //     ilova exchange API orqali oladi. ---
            $loginToken = Str::random(64);

            $user->update([
                'login_token' => $loginToken,
                'login_token_expires_at' => now()->addMinutes(10),
                'login_token_used_at' => null,
            ]);

            Log::info('LoginBotController callback mobile login_token created', [
                'user_id' => $user->id,
                'chat_id' => $user->chat_id,
            ]);

            // Cross-domain: origin cefronline.uz'dan boshqa domenga (teacher-muzaffar.com)
            // yo'naltiramiz — iOS Universal Link shundagina ishga tushadi.
            return redirect()->away(
                "https://teacher-muzaffar.com/auth-callback?login_token={$loginToken}"
            );
        }

        Auth::login($user);
        $user->update([
            'session_id' => session()->getId(),
        ]);

        Log::info('LoginBotController callback login success', [
            'user_id' => $user->id,
            'chat_id' => $user->chat_id,
        ]);

        return redirect()->route('user.dashboard');
    }

    private function handleTextMessage(string $token, int $chatId, string $text): JsonResponse
    {
        $text = trim($text);

        Log::info('LoginBotController handleTextMessage', [
            'chat_id' => $chatId,
            'text' => $text,
        ]);

        if ($text === self::COMMAND_START || str_starts_with($text, self::COMMAND_START . ' ')) {
            $param = trim(substr($text, strlen(self::COMMAND_START)));
            $stateType = in_array($param, ['web', 'mobile']) ? $param : 'web';
            Cache::put("login_bot_state_{$chatId}", $stateType, now()->addMinutes(30));
            Log::info('LoginBotController start command detected', [
                'chat_id' => $chatId,
                'state_type' => $stateType,
            ]);
            return $this->requestPhoneNumber($token, $chatId);
        }

        if ($this->isManualPhoneNumber($text)) {
            Log::info('LoginBotController manual phone detected', [
                'chat_id' => $chatId,
                'text' => $text,
            ]);
            return $this->rejectManualPhoneNumber($token, $chatId);
        }

        $this->sendMessage(
            $token,
            $chatId,
            "Login uchun /start ni bosing va telefon raqamingizni pastdagi tugma orqali yuboring."
        );

        return $this->jsonResponse('unknown_command');
    }

    private function handleContactMessage(string $token, int $chatId, array $message, array $contact): JsonResponse
    {
        $contactUserId = data_get($contact, 'user_id');
        $fromUserId = data_get($message, 'from.id');

        Log::info('LoginBotController handleContactMessage start', [
            'chat_id' => $chatId,
            'contact_user_id' => $contactUserId,
            'from_user_id' => $fromUserId,
        ]);

        if (!$contactUserId || (string) $contactUserId !== (string) $fromUserId) {
            Log::warning('LoginBotController contact rejected because not owned by sender', [
                'chat_id' => $chatId,
                'contact_user_id' => $contactUserId,
                'from_user_id' => $fromUserId,
            ]);
            return $this->rejectManualPhoneNumber($token, $chatId);
        }

        $phoneNumber = $this->normalizePhoneNumber((string) data_get($contact, 'phone_number', ''));
        $user = $this->findUserByPhone($phoneNumber);
        $name = $this->resolveTelegramName($message);
        $username = ltrim((string) data_get($message, 'from.username', ''), '@');

        Log::info('LoginBotController contact normalized data', [
            'chat_id' => $chatId,
            'phone' => $phoneNumber,
            'user_found' => (bool) $user,
            'name' => $name,
            'username' => $username,
        ]);

        if (!$user) {
            Log::info('LoginBotController creating new user from telegram contact', [
                'chat_id' => $chatId,
                'phone' => $phoneNumber,
                'name' => $name,
                'username' => $username,
            ]);

            $user=User::create([
                'name' => $name,
                'username' => $username,
                'mobil_number' => $phoneNumber,
                'password' => Hash::make(Str::random(32)),
            ]);
        }


        $uniq_token = (string) Str::uuid();

        $code = rand(1000, 9999);

        $user->update([
            'chat_id' => $chatId,
            'session_id' => $uniq_token,
            'mobile_verified_at' => Carbon::now(),
            'mobile_verify_code'=>$code
        ]);


        $stateType = Cache::get("login_bot_state_{$chatId}", 'web');

        // Origin (foydalanuvchi tushadigan callback) doim cefronline.uz'da bo'lsin.
        // iOS Universal Link cross-domain redirect uchun: origin=cefronline.uz,
        // yakuniy=teacher-muzaffar.com (same-domain redirect muammosini chetlab o'tadi).
        URL::forceRootUrl('https://cefronline.uz');

        $callbackUrl = URL::temporarySignedRoute(
            'telegram.callback',
            now()->addMinutes(10),
            [
                'uniq_token' => $uniq_token,
                'state_type' => $stateType,
            ]
        );



        $keyboard = [
            'inline_keyboard' => [
                [[
                    'text' => 'Login',
                    'url' => $callbackUrl,
                ]],
            ],
        ];

        $this->sendMessage(
            $token,
            $chatId,
            "Telefon raqamingiz qabul qilindi.\nPastdagi Login tugmasini bosib saytga kiring.",
            $keyboard
        );

        return $this->jsonResponse('contact_verified');
    }

    private function requestPhoneNumber(string $token, int $chatId): JsonResponse
    {
        $keyboard = $this->getPhoneNumberKeyboard();

        Log::info('LoginBotController requestPhoneNumber', [
            'chat_id' => $chatId,
            'keyboard' => $keyboard,
        ]);

        $this->sendMessage(
            $token,
            $chatId,
            "Iltimos, telefon raqamingizni faqat pastdagi tugma orqali yuboring 👇",
            $keyboard
        );

        return $this->jsonResponse('waiting_for_contact');
    }

    private function rejectManualPhoneNumber(string $token, int $chatId): JsonResponse
    {
        Log::info('LoginBotController rejectManualPhoneNumber', [
            'chat_id' => $chatId,
        ]);

        $this->sendMessage(
            $token,
            $chatId,
            "❌ Telefon raqamini qo'lda yozmang. Pastdagi tugma orqali yuboring.",
            $this->getPhoneNumberKeyboard()
        );

        return $this->jsonResponse('invalid_contact');
    }

    private function getPhoneNumberKeyboard(): array
    {
        return [
            'keyboard' => [
                [['text' => '📞 Telefon raqamni yuborish', 'request_contact' => true]],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ];
    }

    private function sendMessage(string $token, int $chatId, string $text, ?array $keyboard = null): void
    {
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        if ($keyboard) {
            $payload['reply_markup'] = $keyboard;
        }

        Log::info('LoginBotController sendMessage request', [
            'chat_id' => $chatId,
            'text' => $text,
            'has_keyboard' => (bool) $keyboard,
            'payload' => $payload,
        ]);

        try {
            $response = Http::retry(2, 1000, throw: false)
                ->timeout(15)
                ->post("https://api.telegram.org/bot{$token}/sendMessage", $payload);

            Log::info('LoginBotController sendMessage response', [
                'chat_id' => $chatId,
                'status' => $response->status(),
                'ok' => $response->json('ok'),
                'body' => $response->json() ?? $response->body(),
            ]);
        } catch (Throwable $e) {
            Log::error('LoginBotController sendMessage exception', [
                'chat_id' => $chatId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function getLoginBotToken(): ?string
    {
        $token = trim((string) optional(
            GlobalSetting::orderByDesc('effective_at')->select('login_bot_token')->first()
        )->login_bot_token);

        Log::info('LoginBotController getLoginBotToken', [
            'has_token' => filled($token),
        ]);

        return $token;
    }

    private function isManualPhoneNumber(string $text): bool
    {
        return (bool) preg_match('/^\+?\d{9,15}$/', $text);
    }

    private function normalizePhoneNumber(string $phoneNumber): string
    {
        $digits = preg_replace('/\D/', '', $phoneNumber);

        if (str_starts_with($digits, '998')) {
            return '+' . $digits;
        }

        return '+998' . ltrim($digits, '0');
    }

    private function findUserByPhone(string $normalizedPhone): ?User
    {
        $normalizedDigits = preg_replace('/\D/', '', $normalizedPhone);
        $user = User::whereRaw("REPLACE(mobil_number, '+', '') = ?", [$normalizedDigits])->first();

        Log::info('LoginBotController findUserByPhone', [
            'normalized_phone' => $normalizedPhone,
            'normalized_digits' => $normalizedDigits,
            'user_found' => (bool) $user,
            'user_id' => $user?->id,
        ]);

        return $user;
    }
    private function findUserByToken(string $token): ?User
    {
        $user = User::where('session_id',$token)->first();

        Log::info('LoginBotController findUserByToken', [
            'token' => $token,
            'user_found' => (bool) $user,
            'user_id' => $user?->id,
        ]);

        return $user;
    }

    private function resolveTelegramName(array $message): string
    {
        $firstName = trim((string) data_get($message, 'from.first_name', ''));
        $lastName = trim((string) data_get($message, 'from.last_name', ''));
        $telegramName = trim($firstName . ' ' . $lastName);

        return $telegramName !== '' ? $telegramName : ' ';
    }

    private function getNotRegisteredMessage(string $phoneNumber): string
    {
        return "❌ {$phoneNumber} raqami bo'yicha foydalanuvchi topilmadi.";
    }

    private function jsonResponse(string $status, array $additional = []): JsonResponse
    {
        return response()->json(array_merge(['status' => $status], $additional));
    }
}
