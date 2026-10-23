# V2 Autentifikatsiya — Ro'yxatdan o'tish, Tasdiqlash va Parol tiklash (to'liq hujjat)

Bu hujjat mobil ilova foydalanuvchisi **telefon raqam** yoki **email** orqali
qanday ishlashini — ro'yxatdan o'tish, kodni tasdiqlash va parolni tiklash —
butun logikasi bilan boshidan oxirigacha tushuntiradi.

**Qamrab olingan endpointlar (`routes/api.php`, public guruh):**

| Metod | URL | Metod nomi | Vazifasi |
|-------|-----|-----------|----------|
| POST | `/v2/register` | `register` | Ro'yxatdan o'tish + tasdiqlash kodini yuborish |
| POST | `/v2/verify_sms` | `verify_sms` | Register kodini tekshirish + auth token berish |
| POST | `/v2/forgotpassword` | `forgotpassword` | Parol tiklash uchun kod yuborish |
| POST | `/v2/verify_password` | `verify_password` | Parol tiklash kodini tekshirish |
| POST | `/v2/resetPassword` | `resetPassword` | Yangi parol o'rnatish + auth token berish |

**Asosiy fayllar:**
- Controller: `app/Http/Controllers/Api/V2/Auth/AuthApiController.php`
- Register validatsiyasi: `app/Http/Requests/Auth/UserRegisterApiRequest.php`
- Kod yuborish job'lari: `app/Jobs/SendSmsJob.php`, `app/Jobs/SendEmailVerification.php`
- Javob resursi: `app/Http/Resources/Admin/UserResource.php`

---

## 1. Umumiy g'oya va umumiy tamoyillar

### 1.1. Ikki oqim — bitta maydon

Har bir amal telefon (`type = 1`) yoki email (`type = 0`) orqali ishlaydi.
Foydalanuvchi ilova tomonda **bitta maydon** yuboradi (`mobile_number_or_email`),
backend o'zi telefonmi/emailmi ekanini aniqlab, `type` ni belgilaydi:

- Ichida **harf yoki `@`** bo'lsa → **email** (`type = 0`)
- Aks holda → **telefon** (`type = 1`), va `+998XXXXXXXXX` formatiga keltiriladi

Faqat `forgotpassword` bitta `mobile_number_or_email` maydonini oladi;
`verify_password` va `resetPassword` esa endi **aniq `type` + `mobile_number`
yoki `email`** maydonlarini kutadi (pastda batafsil).

### 1.2. Tasdiqlash kodi (verify_code)

- Har safar **4 xonali** kod generatsiya qilinadi: `rand(1000, 9999)`.
- Telefon uchun `mobile_verify_code`, email uchun `email_verify_code`
  ustunlariga yoziladi.
- Kod **navbat (queue)** orqali yuboriladi:
  - SMS → `SendSmsJob` → `sms` queue
  - Email → `SendEmailVerification` → `email` queue
- Kod matni: `"Kodni hech kimga bermang! TM school ilovasiga kirish uchun tasdiqlash kodi: {code}"`

> ⚠️ Kod DB'da **ochiq** saqlanadi va **amal qilish muddati (expiry) yo'q** —
> faqat yangi kod so'ralganda eskisi ustiga yoziladi.

### 1.3. Tasdiqlanish holati

- `mobile_verified_at` / `email_verified_at` — `null` bo'lsa hisob
  **tasdiqlanmagan**, `Carbon::now()` bo'lsa **tasdiqlangan**.
- `is_verified` middleware'i himoyalangan endpointlarda shu holatni tekshiradi.

### 1.4. Auth token

- Tasdiqlash muvaffaqiyatli tugagach `createToken('auth_token')->plainTextToken`
  orqali **Sanctum** tokeni beriladi.
- Keyingi himoyalangan so'rovlarda `Authorization: Bearer {token}` yuboriladi.

---

## 2. `POST /v2/register` — Ro'yxatdan o'tish

### 2.1. So'rov (`multipart/form-data`)

| Maydon | Majburiy | Tavsif |
|--------|----------|--------|
| `name` | ✅ | Ism. `string`, max 255 |
| `surname` | ✅ | Familiya. `string`, max 255 |
| `mobile_number_or_email` | ✅ | Telefon yoki email — bitta maydon |
| `password` | ✅ | Parol, min 6 belgi |
| `password_confirmation` | ✅ | Parolni takrorlash (`confirmed`) |
| `birth_date` | ❌ | `YYYY-MM-DD` |
| `gender` | ❌ | integer |
| `city_id` | ❌ | `cities` jadvalida mavjud bo'lishi kerak |
| `image` | ❌ | Profil rasmi |

### 2.2. Normalizatsiya (`UserRegisterApiRequest::prepareForValidation`)

Validatsiyadan **oldin** `mobile_number_or_email` tozalanadi va `type` /
`mobile_number` / `email` avtomatik to'ldiriladi:

```php
$raw = trim($this->input('mobile_number_or_email'));

if (preg_match('/[a-zA-Z@]/', $raw)) {           // harf yoki @ bor → EMAIL
    type = 0; email = strtolower($raw); mobile_number = null; return;
}
$digits = preg_replace('/\D+/', '', $raw);        // faqat raqamlar
if (strlen($digits) > 9) $digits = substr($digits, -9);  // oxirgi 9 ta
$normalized = strlen($digits) === 9 ? '+998'.$digits : $digits;
type = 1; mobile_number = $normalized; email = null;
```

| Kirish | `type` | `mobile_number` | `email` |
|--------|:------:|-----------------|---------|
| `ali@example.com` | 0 | `null` | `ali@example.com` |
| `+998901234567` | 1 | `+998901234567` | `null` |
| `901234567` | 1 | `+998901234567` | `null` |
| `998901234567` | 1 | `+998901234567` | `null` |

### 2.3. Validatsiya qoidalari

```
name                     required | string | max:255
surname                  required | string | max:255
mobile_number_or_email   required | string
type                     required | in:0,1
mobile_number            nullable | required_if:type,1 | string | size:13 | starts_with:+998
email                    nullable | required_if:type,0 | email
password                 required | string | min:6 | confirmed
birth_date               nullable | date
gender                   nullable
city_id                  nullable | exists:cities,id
```

> ℹ️ `image` uchun `messages()` da xato matnlari bor, lekin `rules()` da qoida
> **yozilmagan** — hozircha rasm formati/hajmi tekshirilmaydi.

### 2.4. Controller logikasi (`register`)

1. `$code = rand(1000, 9999)`.
2. Rasm bo'lsa `public/storage/userImage/` ga saqlanib, to'liq URL yasaladi.
3. Bunday user bor-yo'qligi tekshiriladi (`type=1` → `mobil_number`, `type=0` → `email`).
4. **User yo'q** → `User::create(...)` (parol `bcrypt`), `mobile_verify_code`
   yoki `email_verify_code = $code`, so'ng `roles()->sync(2)` (role_id = 2).
5. **User bor:**
   - **Tasdiqlangan** (`...verified_at !== null`) → xato:
     `{"success": false, "errors": {"mobil_number": ["Telefon raqam bor"]}}`
     (email: `"email": ["Email allaqachon mavjud."]`).
   - **Tasdiqlanmagan** → ma'lumotlar `update` qilinadi va yangi kod yoziladi
     (foydalanuvchi qaytadan boshlashi mumkin).
6. Kod yuboriladi (`type=1` → `SendSmsJob`, `type=0` → `SendEmailVerification`).
7. Javob (parol maydonlari olib tashlanadi).

> ⚠️ **Yangilashda parol:** create'da `bcrypt(...)`, lekin mavjud tasdiqlanmagan
> user'ni update'da `'password' => $data['password']` (ochiq) yozilgan —
> xeshlanishi `User` modeli cast'iga bog'liq.

### 2.5. Muvaffaqiyatli javob (200)

```json
{
  "success": true,
  "message": "Sms jo'natildi",
  "data": {
    "name": "Ali", "surname": "Valiyev", "type": 1,
    "mobile_number": "+998901234567", "email": null,
    "birth_date": "2000-01-15", "gender": 1, "city_id": 3,
    "image": "https://teacher-muzaffar.com/storage/userImage/xxx.png"
  }
}
```
Email holatida `message`: `"Emailga tasdiqlash kodi yuborildi"`.

> ⚠️ Bu javobda user'ning DB `id`'si **qaytmaydi** — `data` faqat yuborilgan
> so'rovning tozalangan nusxasi.

---

## 3. `POST /v2/verify_sms` — Register kodini tasdiqlash

### 3.1. So'rov (`application/json`)

| Maydon | Majburiy | Tavsif |
|--------|----------|--------|
| `type` | ✅ | `1` = telefon, `0` = email |
| `mobile_number` | `type=1` bo'lsa | `+998901234567` |
| `email` | `type=0` bo'lsa | `user@gmail.com` |
| `verify_code` | ✅ | 4 xonali kod |
| `device_id` | ❌ | Qurilma id (ixtiyoriy) |

Validatsiya: `type required|in:1,0`, `mobile_number required_if:type,1`,
`email required_if:type,0|email`, `verify_code required`.

> ⚠️ Bu yerda telefon **normalizatsiya qilinmaydi** — ilova register javobidagi
> aniq `mobile_number` (`+998...`) qiymatini o'zgartirmasdan yuborishi shart.

### 3.2. Logika

- `type=1`: `mobil_number` bo'yicha user topiladi; agar
  `verify_code == mobile_verify_code` **va** `mobile_number == mobil_number`
  bo'lsa → `mobile_verified_at = now()`, `device_id` yoziladi.
- `type=0`: `email` bo'yicha user; `verify_code == email_verify_code` va
  `email == email` bo'lsa → `email_verified_at = now()`.
- To'g'ri bo'lsa: `Auth::login`, `createToken('auth_token')`.
- Xato/topilmasa: `{"success": false, "errors": "Kod xato kiritildi"}`.

### 3.3. Muvaffaqiyatli javob (200)

```json
{
  "success": true,
  "message": "User ro'yhatdan muvaffaqiyatli o'tdi",
  "auth_token": "12|xxxxxxxx",
  "data": {
    "id": 25, "name": "Ali", "mobil_number": "+998901234567",
    "email": null, "gender": 1, "birth_date": "2000-01-15",
    "surname": "Valiyev", "image": "https://.../avatar.png", "city_id": 3
  }
}
```
`data` — `UserResource` (id, name, mobil_number, email, gender, birth_date,
surname, image, city_id).

---

## 4. `POST /v2/forgotpassword` — Parol tiklash kodini yuborish

### 4.1. So'rov (`application/json`)

| Maydon | Majburiy | Tavsif |
|--------|----------|--------|
| `mobile_number_or_email` | ✅ | Telefon yoki email — bitta maydon |

Validatsiya: `mobile_number_or_email required|string`
(`Telefon raqam yoki email kiritilmadi!`).

### 4.2. Logika

Bu endpointda normalizatsiya **controller ichida** qilinadi (`FormRequest` emas):

```php
$raw = trim($request->mobile_number_or_email);

if (filter_var($raw, FILTER_VALIDATE_EMAIL)) {     // to'g'ri email → type 0
    $type = 0; $email = strtolower($raw);
} else {                                            // aks holda → telefon
    $digits = preg_replace('/\D+/', '', $raw);
    if (strlen($digits) > 9) $digits = substr($digits, -9);
    $mobile_number = strlen($digits) === 9 ? '+998'.$digits : $raw;
}
```

**Telefon (`type = 1`):**
1. Format tekshiriladi: `/^\+998\d{9}$/`. Noto'g'ri bo'lsa 422:
   `{"errors": {"mobile_number_or_email": ["Telefon raqam noto‘g‘ri formatda"]}}`.
2. User qidiriladi; topilmasa 422: `{"errors": "Bunday foydalanuvchi mavjud emas!"}`.
3. `code = rand(1000,9999)` → `mobile_verify_code` ga yoziladi → `SendSmsJob`.

**Email (`type = 0`):**
1. User `email` bo'yicha qidiriladi; topilmasa 422: `Bunday foydalanuvchi mavjud emas!`.
2. `code` → `email_verify_code` → `SendEmailVerification`.

> ⚠️ `forgotpassword` da `filter_var(..., FILTER_VALIDATE_EMAIL)` ishlatiladi
> (register'dagi "harf yoki @ bor" tekshiruvidan farqli). Ya'ni email deb
> hisoblanishi uchun qiymat **to'liq yaroqli email** bo'lishi kerak.

### 4.3. Muvaffaqiyatli javob (200)

```json
{
  "success": true,
  "data": {
    "type": 1,
    "mobile_number": "+998901234567",
    "email": null,
    "code": 1234,
    "message": "Userga sms jonatildi!"
  }
}
```
Email holatida: `type: 0`, `email` to'ldiriladi, `message: "Emailga tasdiqlash kodi yuborildi!"`.

> ⚠️ **Diqqat:** bu javob `code` (4 xonali kod) ni **ochiq qaytaradi**. Bu
> qulaylik uchun bo'lishi mumkin, ammo xavfsizlik nuqtai nazaridan kodni
> javobda qaytarmaslik tavsiya etiladi.

---

## 5. `POST /v2/verify_password` — Parol tiklash kodini tekshirish

### 5.1. So'rov (`application/json`)

| Maydon | Majburiy | Tavsif |
|--------|----------|--------|
| `type` | ✅ | `1` = telefon, `0` = email |
| `mobile_number` | `type=1` bo'lsa | `+998901234567` |
| `email` | `type=0` bo'lsa | `user@gmail.com` |
| `verify_code` | ✅ | `forgotpassword` yuborgan kod |

Validatsiya qoidalari (`exclude_unless` bilan — mos kelmagan maydon butunlay
o'tkazib yuboriladi):
```
type          required | in:1,0
mobile_number exclude_unless:type,1 | required | regex:/^\+998\d{9}$/ | exists:users,mobil_number
email         exclude_unless:type,0 | required | email | max:255 | exists:users,email
verify_code   required
```
Xato xabarlari: `Telefon raqam noto‘g‘ri formatda!`, `Bunday telefon raqam
mavjud emas!`, `Email format noto‘g‘ri!`, `Bunday email mavjud emas!`,
`verify_code kiritilmadi!`.

### 5.2. Logika

- `type=1`: user `mobil_number` bo'yicha; `verify_code == mobile_verify_code` tekshiriladi.
- `type=0`: user `email` bo'yicha; `verify_code == email_verify_code`.
- Kod to'g'ri bo'lsa — mos `verified_at` ustuni `Carbon::now()` ga yangilanadi
  va javob:
  ```json
  { "success": true, "data": "Parolni ozgartirish mumkin" }
  ```
- Kod xato bo'lsa 422:
  ```json
  { "success": false, "errors": "Kod xato kiritildi" }
  ```

> ℹ️ Bu bosqich token bermaydi — u faqat "kod to'g'ri, endi parolni
> yangilash mumkin" degan ruxsatni beradi. Keyingi qadam — `resetPassword`.

---

## 6. `POST /v2/resetPassword` — Yangi parol o'rnatish

### 6.1. So'rov (`application/json`)

| Maydon | Majburiy | Tavsif |
|--------|----------|--------|
| `type` | ✅ | `1` = telefon, `0` = email |
| `mobile_number` | `type=1` bo'lsa | `+998901234567` |
| `email` | `type=0` bo'lsa | `user@gmail.com` |
| `password` | ✅ | Yangi parol, min 6 belgi |

Validatsiya (`verify_password` bilan bir xil `type`/`mobile_number`/`email`
qoidalari + `password required|min:6`, xabar: `Parol 6 tadan kam bolmasligi kerak`).

### 6.2. Logika

- `type` ga qarab user `mobil_number` yoki `email` bo'yicha topiladi.
- Topilsa: `password` → `Hash::make(...)` bilan yangilanadi, `Auth::login`,
  `createToken('auth_token')`:
  ```json
  {
    "success": true,
    "message": "Parolni muvaffaqiyatli o'zgartirildi",
    "auth_token": "12|xxxxxxxx"
  }
  ```
- Topilmasa: `{"success": false, "errors": "Bunday foydalanuvchi mavjud emas!"}`.

> ⚠️ `resetPassword` **`verify_password` dan mustaqil** ishlaydi — u kod
> tasdiqlanganini alohida tekshirmaydi (faqat user borligini). Xavfsizlik uchun
> ilova oqimni to'g'ri ketma-ketlikda chaqirishi kerak:
> `forgotpassword → verify_password → resetPassword`.

---

## 7. To'liq oqimlar (diagrammalar)

### 7.1. Ro'yxatdan o'tish

```
Ilova                         Backend                        DB / Queue
  │  POST /v2/register          │                                │
  ├────────────────────────────►│ normalize → type, mobile/email │
  │                             │ validate; code=rand(1000,9999) │
  │                             │ User::create/update ──────────►│ (verified_at = null)
  │                             │ SendSms/Email job ────────────►│ (queue: sms/email)
  │  200 {success, message, data}│                               │
  │◄────────────────────────────┤                                │
  │  [foydalanuvchi kodni kiritadi]                              │
  │  POST /v2/verify_sms         │                                │
  ├────────────────────────────►│ kodni solishtiradi ───────────►│
  │                             │ verified_at = now() ──────────►│
  │                             │ createToken('auth_token')      │
  │  200 {success, auth_token, data}                             │
  │◄────────────────────────────┤                                │
  │  [endi Bearer auth_token bilan API'ga kiradi]                │
```

### 7.2. Parolni tiklash

```
Ilova                         Backend                        DB / Queue
  │  POST /v2/forgotpassword     │                                │
  ├────────────────────────────►│ normalize; user bor-yo'q?      │
  │                             │ code → verify_code ustuni ────►│
  │                             │ SendSms/Email ────────────────►│ (queue)
  │  200 {success, data:{code,...}}                              │
  │◄────────────────────────────┤                                │
  │  POST /v2/verify_password    │                                │
  ├────────────────────────────►│ verify_code solishtiriladi     │
  │                             │ verified_at = now() ──────────►│
  │  200 {success, "Parolni ozgartirish mumkin"}                │
  │◄────────────────────────────┤                                │
  │  POST /v2/resetPassword      │                                │
  ├────────────────────────────►│ password = Hash::make() ──────►│
  │                             │ createToken('auth_token')      │
  │  200 {success, auth_token}   │                                │
  │◄────────────────────────────┤                                │
```

---

## 8. Muhim eslatmalar va ehtiyot choralari

1. **Register atomik emas** — user darhol yaratiladi; verify yakunlanmasa
   `verified_at = null` "yarim" hisob qoladi (qayta register'da yangilanadi).
2. **Kodlar ochiq va muddatsiz** (`mobile_verify_code` / `email_verify_code`) —
   expiry qo'shish tavsiya etiladi.
3. **Kod yuborish queue'ga bog'liq** — `queue:work --queue=sms,email` ishlamasa,
   kod yetib bormaydi (javob baribir `success: true` bo'lishi mumkin).
4. **`verify_sms` da telefon normalizatsiya qilinmaydi** — aniq `+998...` qiymat
   yuborilishi shart.
5. **`forgotpassword` kodni javobda ochiq qaytaradi** (5-bo'lim, 4.3 ⚠️) —
   xavfsizlik uchun olib tashlash tavsiya etiladi.
6. **`resetPassword` verify holatini tekshirmaydi** — oqim tartibini ilova
   ta'minlashi kerak (6.2 ⚠️).
7. **Rasm validatsiyasi yo'q** (register) — `rules()` da `image` qoidasi yo'q.
8. **Yangi user roli** — har doim `role_id = 2`.
9. **`register` "email"ni harf/@ bo'yicha, `forgotpassword` esa
   `FILTER_VALIDATE_EMAIL` bo'yicha** aniqlaydi — mezonlar biroz farq qiladi.

---

## 9. Tez sinov (cURL)

```bash
# Register (telefon)
curl -X POST https://<host>/api/v2/register \
  -F "name=Ali" -F "surname=Valiyev" \
  -F "mobile_number_or_email=+998901234567" \
  -F "password=secret123" -F "password_confirmation=secret123"

# Verify register (telefon)
curl -X POST https://<host>/api/v2/verify_sms \
  -H "Content-Type: application/json" \
  -d '{"type":1,"mobile_number":"+998901234567","verify_code":"1234"}'

# Forgot password (email)
curl -X POST https://<host>/api/v2/forgotpassword \
  -H "Content-Type: application/json" \
  -d '{"mobile_number_or_email":"ali@example.com"}'

# Verify password (email)
curl -X POST https://<host>/api/v2/verify_password \
  -H "Content-Type: application/json" \
  -d '{"type":0,"email":"ali@example.com","verify_code":"1234"}'

# Reset password (email)
curl -X POST https://<host>/api/v2/resetPassword \
  -H "Content-Type: application/json" \
  -d '{"type":0,"email":"ali@example.com","password":"newsecret123"}'
```

---

## 10. Bog'liq oqimlar

- Google / Telegram orqali kirish: **`docs/AUTH_GOOGLE_TELEGRAM.md`**
- Login: `POST /v3/login` (`login_v2`) — tasdiqlangan user parol bilan kiradi
- Barcha metodlar bitta controllerda: `AuthApiController`.
