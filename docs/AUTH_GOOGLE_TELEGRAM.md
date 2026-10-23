# Google va Telegram orqali Flutter ilovaga kirish (auth flow)

Bu hujjat Flutter mobil ilova foydalanuvchisi tizimga **faqat ikkita yo'l** orqali —
Google va Telegram — qanday kirishini, va ikkalasi nega bitta umumiy nuqtaga
(`exchange-token`) yig'ilishini tushuntiradi.

## 1. Asosiy g'oya

Flutter ilova o'zi to'g'ridan-to'g'ri Google yoki Telegram bilan gaplashmaydi.
Ikkala provayder ham brauzer/Telegram ichida ishlaydi va oxirida bitta umumiy
"universal link" orqali ilovaga qaytadi:

```
https://cefronline.uz/auth-callback?login_token={64 belgili bir martalik token}
```

Ilova shu linkdagi `login_token`ni ushlab oladi va uni **bitta** API endpoint —
`POST /v2/auth/exchange-token` orqali haqiqiy Sanctum auth tokenga almashtiradi.
Ya'ni Google ham, Telegram ham — natijada bitta joyga (`exchange-token`) olib
keladi, ilova faqat shu bitta endpoint bilan ishlashni bilsa yetarli.

```
Google login  ─┐
                ├─► login_token (universal link) ─► POST /v2/auth/exchange-token ─► auth_token (Sanctum)
Telegram login ─┘
```

## 2. Google orqali kirish

**Fayllar:** `app/Http/Controllers/Api/V2/Auth/AuthApiController.php`,
`app/Http/Controllers/Auth/GoogleAuthController.php`

1. Ilova `GET /v2/google/link` ni chaqiradi.
   - Backend Google OAuth redirect URL qaytaradi (`state=mobile` bilan belgilangan).
2. Ilova shu URLni brauzerda (yoki `WebView`/`flutter_web_auth`da) ochadi,
   foydalanuvchi Google akkaunti bilan tasdiqlaydi.
3. Google `GET /auth/google/callback` ga qaytaradi (`GoogleAuthController::callback`).
   - Agar user email bo'yicha topilmasa — yangi user yaratiladi (rol: `2`).
   - `state=mobile` bo'lgani uchun bir martalik `login_token` generatsiya qilinadi
     (`Str::random(64)`, 10 daqiqa amal qiladi) va userga saqlanadi.
   - Foydalanuvchi universal linkka redirect qilinadi:
     `https://cefronline.uz/auth-callback?login_token=...`
4. Flutter ilova bu linkni ushlab, `login_token`ni oladi va 3-bo'limdagi
   `exchange-token` API'siga yuboradi.

> Eslatma: `GET /v2/auth/google/callback` (AuthApiController) — bu alohida,
> to'g'ridan-to'g'ri JSON qaytaruvchi endpoint, hozircha web `state=mobile`
> oqimida ishlatilmaydi; asosiy mobil oqim yuqoridagi universal link + `login_token`
> orqali ishlaydi.

## 3. Telegram orqali kirish

**Fayl:** `app/Http/Controllers/LoginBotController.php`

1. Ilova `GET /v2/google/link` javobidagi `telegram_link` (yoki
   `GET /auth/telegram`) orqali foydalanuvchini Telegram botga yo'naltiradi:
   `https://t.me/{bot_username}?start=mobile`.
2. Foydalanuvchi botda `/start mobile` bosadi → bot state turini (`mobile`)
   cache'da saqlaydi (`login_bot_state_{chatId}`) va telefon raqamini so'raydi
   (faqat "Telefon raqamni yuborish" tugmasi orqali, qo'lda yozishga ruxsat yo'q).
3. Foydalanuvchi kontaktini yuboradi → `handleContactMessage`:
   - Shu raqam bo'yicha user topiladi, topilmasa — yangi user yaratiladi.
   - Bir martalik `uniq_token` (`Str::uuid()`) yaratilib `session_id` ga yoziladi.
   - Botda imzolangan (`temporarySignedRoute`) "Login" tugmasi yuboriladi:
     `GET /auth/telegram/callback?uniq_token=...&state_type=mobile`.
4. Foydalanuvchi "Login" tugmasini bosadi → `LoginBotController::callback`:
   - Imzo tekshiriladi, `uniq_token` bo'yicha user topiladi.
   - `state_type=mobile` bo'lgani uchun (Google oqimidagi bilan **bir xil**
     mexanizm) bir martalik `login_token` yaratiladi va userga saqlanadi.
   - Foydalanuvchi xuddi Google oqimidagi kabi universal linkka redirect qilinadi:
     `https://cefronline.uz/auth-callback?login_token=...`
5. Flutter ilova linkni ushlaydi va xuddi Google oqimidagi kabi
   `exchange-token` API'siga yuboradi.

## 4. Umumiy nuqta: `POST /v2/auth/exchange-token`

**Fayl:** `AuthApiController::exchange_login_token`

Google va Telegram — ikkalasi ham shu yerga kelib qo'shiladi. Ilova faqat shu
bitta endpoint bilan gaplashadi:

- **So'rov:** `{ "login_token": "...", "device_id": "..." (ixtiyoriy) }`
- **Tekshiruvlar:**
  - Token topilmasa → `Token topilmadi` (422)
  - Token allaqachon ishlatilgan bo'lsa (`login_token_used_at !== null`) →
    `Token allaqachon ishlatilgan` (422)
  - Muddati o'tgan bo'lsa (10 daqiqadan ko'p) → `Token muddati tugagan` (422)
- **Muvaffaqiyatli bo'lsa:**
  - Token bir martalik ishlatilgan deb belgilanadi (`login_token` `null`
    qilinadi, `login_token_used_at` yoziladi) — qayta ishlatib bo'lmaydi.
  - Sanctum `auth_token` yaratiladi va foydalanuvchi ma'lumotlari, aktiv
    premium reja bilan birga qaytariladi (login javobi `/v3/login` bilan bir xil
    formatda: `token`, `premium`, `active_plan`, `data`).

## 5. Ishlatiladigan `users` jadvali maydonlari

| Maydon | Vazifasi |
|---|---|
| `login_token` | Universal link orqali ilovaga yuboriladigan bir martalik token (64 belgi) |
| `login_token_expires_at` | Token yaratilgandan 10 daqiqa keyin muddati tugaydi |
| `login_token_used_at` | Token ishlatilganini belgilaydi (qayta ishlatishni oldini oladi) |
| `chat_id` | Telegram chat identifikatori |
| `session_id` | Telegram login oqimida vaqtinchalik `uniq_token` sifatida ishlatiladi |

## 6. Bugungi tuzatishlar

- **`GoogleAuthController.php`** — noto'g'ri import (`http\Client\Request`)
  `Illuminate\Http\Request` bilan almashtirildi. Bu import xato bo'lgani
  sabab `callback()` metodidagi `Request $request` type-hint aslida hech
  narsani tekshirmayotgan/ishlamayotgan edi, PHP class topilmasa fatal error
  berishi mumkin edi.
- **`UserRegisterApiRequest.php`** — `image` maydoni uchun validatsiya qoidasi
  (`nullable|image|mimes:...|max:4096`) olib tashlandi, chunki ro'yxatdan
  o'tish endi rasm yubormaydi (Google/Telegram orqali kirishda rasm avtomatik
  provayderdan olinadi yoki umuman talab qilinmaydi).

## 7. Flutter tomonidan qisqacha qo'llanma

1. `GET /v2/google/link` chaqiring → `google_link` va `telegram_link` oling.
2. Foydalanuvchiga ikkita tugma ko'rsating: "Google bilan kirish" va
   "Telegram bilan kirish".
3. Qaysi birini bossa, mos linkni tashqi brauzer/Telegram ilovasida oching.
4. `cefronline.uz/auth-callback?login_token=...` deep link/universal link
   sifatida ilovaga ushlanadi (bu domen uchun App Links / Universal Links
   sozlangan bo'lishi kerak).
5. Ushlangan `login_token`ni `POST /v2/auth/exchange-token` ga yuboring.
6. Javobdagi `token`ni saqlang va keyingi barcha so'rovlarda
   `Authorization: Bearer {token}` sifatida ishlating.
