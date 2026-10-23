<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class GoogleAuthController extends Controller
{


    public function redirect()
    {
        return Socialite::driver('google')
            ->stateless()
            ->with(['state' => 'web'])
            ->redirect();
    }
    public function callback(Request $request)
    {
        // Google xato qaytargan yoki code yo'q bo'lsa (foydalanuvchi bekor qilgan,
        // callback URL to'g'ridan-to'g'ri ochilgan, oqim buzilgan va h.k.)
        if ($request->has('error') || !$request->has('code')) {
            return redirect()->route('login')
                ->with('error', 'Google orqali kirish bekor qilindi yoki xato yuz berdi');
        }

        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')
                ->with('error', 'Google orqali kirishda xatolik');
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            $email=$googleUser->getEmail();

            $user = User::create([
                'name' => $googleUser->getName() ?? $googleUser->getNickname(),
                'email' => $email,
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(32)),
            ]);
            $user->roles()->sync(2);

        }

        if ($request->input('state') === 'mobile') {
            // --- MOBILE: bir martalik qisqa muddatli login_token yaratib,
            //     universal link orqali ilovaga qaytaramiz. Haqiqiy auth token
            //     ilova exchange API orqali oladi. ---
            $loginToken = Str::random(64);

            $user->update([
                'login_token' => $loginToken,
                'login_token_expires_at' => now()->addMinutes(10),
                'login_token_used_at' => null,
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

        return redirect()->route('user.dashboard');
    }

    /**
     * Universal link brauzerda ochilsa (app o'rnatilmagan / iOS Universal Link
     * ishlamasa): login_token ni web sessiyaga almashtirib, foydalanuvchini
     * dashboardga kiritamiz.
     */
    /**
     * Universal/App Link target (/auth-callback).
     *
     * MUHIM: bu yerda login_token'ni ISHLATMAYMIZ. Aks holda brauzer (Android
     * Chrome) app link'ni ushlashdan oldin sahifani yuklab, bir martalik token'ni
     * "yeb" qo'yadi va ilova exchange-token chaqirganda "Token topilmadi" oladi.
     *
     * Shuning uchun bu sahifa faqat interstitial: ilova o'rnatilgan bo'lsa app
     * link ilovani ochadi va token'ni API orqali ilova oladi. Ilova ochilmasa,
     * foydalanuvchi "Brauzerda davom etish" tugmasi orqali web login qiladi
     * (alohida /web-login path — u app link'ga bog'lanmagan).
     */
    public function auth_callback(Request $request)
    {
        $loginToken = $request->query('login_token');

        if (!$loginToken) {
            return redirect()->route('login')->with('error', 'Login token topilmadi');
        }

        $webLoginUrl = route('web.login.token', ['login_token' => $loginToken]);

        return response(
            $this->interstitialHtml($webLoginUrl, $loginToken)
        )->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Web login — faqat foydalanuvchi interstitial'da tugma bosganda.
     * Bu path app link'ga bog'lanmagan (/web-login), shuning uchun brauzer
     * token'ni faqat shu yerda, aniq foydalanuvchi harakati bilan ishlatadi.
     */
    public function web_login_token(Request $request)
    {
        $loginToken = $request->query('login_token');

        if (!$loginToken) {
            return redirect()->route('login')->with('error', 'Login token topilmadi');
        }

        $user = User::where('login_token', $loginToken)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Token topilmadi');
        }

        if ($user->login_token_used_at !== null) {
            return redirect()->route('login')->with('error', 'Token allaqachon ishlatilgan');
        }

        if ($user->login_token_expires_at === null || now()->greaterThan($user->login_token_expires_at)) {
            return redirect()->route('login')->with('error', 'Token muddati tugagan');
        }

        // bir martalik: ishlatilgan deb belgilaymiz va tokenni o'chiramiz
        $user->update([
            'login_token_used_at' => now(),
            'login_token' => null,
        ]);

        Cache::forget('user_permissions_' . $user->id);

        Auth::login($user, true);
        $request->session()->regenerate();

        $user->update([
            'session_id' => session()->getId(),
        ]);

        return redirect()->route('user.dashboard');
    }

    private function interstitialHtml(string $webLoginUrl, string $loginToken): string
    {
        $safeUrl = htmlspecialchars($webLoginUrl, ENT_QUOTES);
        // Custom scheme — Telegram ichki brauzeri (WebView) ichidan ham ilovani
        // ocha oladi (universal link WebView'dan ishlamaydi). Scheme nomi Flutter
        // Info.plist / AndroidManifest bilan BIR XIL bo'lishi shart.
        $schemeUrl = 'tmschool://auth-callback?login_token=' . rawurlencode($loginToken);
        $safeScheme = htmlspecialchars($schemeUrl, ENT_QUOTES);

        return <<<HTML
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirish...</title>
    <style>
        body{font-family:-apple-system,Segoe UI,Roboto,sans-serif;background:#f5f6f8;
            display:flex;min-height:100vh;align-items:center;justify-content:center;margin:0;padding:24px}
        .card{background:#fff;border-radius:16px;padding:32px 24px;max-width:360px;width:100%;
            box-shadow:0 8px 30px rgba(0,0,0,.08);text-align:center}
        h1{font-size:18px;margin:0 0 8px;color:#111}
        p{font-size:14px;color:#666;margin:0 0 24px;line-height:1.5}
        a.btn{display:block;color:#fff;text-decoration:none;padding:14px;border-radius:12px;
            font-size:15px;font-weight:600;margin-bottom:12px}
        a.primary{background:#2563eb}
        a.secondary{background:#6b7280}
        .spinner{width:36px;height:36px;border:3px solid #e5e7eb;border-top-color:#2563eb;
            border-radius:50%;margin:0 auto 20px;animation:spin 1s linear infinite}
        @keyframes spin{to{transform:rotate(360deg)}}
    </style>
</head>
<body>
    <div class="card">
        <div class="spinner"></div>
        <h1>Ilovaga qaytmoqda...</h1>
        <p>Agar ilova avtomatik ochilmasa, quyidagi tugmani bosing.</p>
        <a class="btn primary" href="{$safeScheme}">Ilovani ochish</a>
        <a class="btn secondary" href="{$safeUrl}">Brauzerda davom etish</a>
    </div>
    <script>
        // Ilovani custom scheme orqali avtomatik ochishga urinamiz.
        // Telegram WebView ichidan ham ishlaydi (OS darajasidagi scheme).
        setTimeout(function () {
            window.location.href = "{$safeScheme}";
        }, 300);
    </script>
</body>
</html>
HTML;
    }

}
