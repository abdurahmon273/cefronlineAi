# `is_uzbekistan` — Flutter uchun qo'llanma (API v2)

## Qanday aniqlanadi (backend)

```php
$location = Location::get($request->ip());
$is_uzbekistan = $location && $location->countryName == 'Uzbekistan';

// Test akkaunt (App Store review uchun) — har doim false
if ($user->mobil_number == '+998941070524') {
    $is_uzbekistan = false;
}
```

- Qiymat **foydalanuvchi IP manzili** bo'yicha aniqlanadi (VPN bo'lsa o'zgaradi).
- Location aniqlanmasa → `false`.
- `+998941070524` test raqami uchun doim `false` (faqat login / exchange-token / check_premium'da; `google/link`'da bu tekshiruv yo'q).

---

## Qaysi API'larda bor

| # | Method | Endpoint | Auth | Controller | `is_uzbekistan` joyi |
|---|--------|----------|------|------------|----------------------|
| 1 | POST | `/api/v3/login` | ❌ | `AuthApiController@login_v2` | root |
| 2 | GET  | `/api/v2/google/link` | ❌ | `AuthApiController@google_link` | `data.is_uzbekistan` |
| 3 | POST | `/api/v2/auth/exchange-token` | ❌ | `AuthApiController@exchange_login_token` | root |
| 4 | GET  | `/api/v2/check_premium` | ✅ Bearer | `PremiumApiController@check_premium` | root |

> Test raqam override (`+998941070524`) — 1, 3, 4 da bor, 2 da yo'q.

---

## 1. `POST /api/v3/login`

**Request**
```json
{
  "type": 1,
  "mobile_number": "+998901234567",
  "email": null,
  "password": "password1115",
  "device_id": "device-123"
}
```

**Response 200**
```json
{
  "success": true,
  "messages": "Foydalanuvchi logindan muvaffaqiyatli o'tdi",
  "token": "1|tokenstring",
  "is_uzbekistan": true,
  "premium": 1,
  "active_plan": { "...": "..." },
  "data": { "...": "user" }
}
```

---

## 2. `GET /api/v2/google/link`

⚠️ Bu yerda `is_uzbekistan` **`data` ichida**.

```json
{
  "data": {
    "is_uzbekistan": true,
    "google_link": "https://accounts.google.com/...",
    "telegram_link": "https://t.me/cefr_online_support_login_bot?start=mobile"
  },
  "message": "google link va telegram link"
}
```

---

## 3. `POST /api/v2/auth/exchange-token`

Javob formati login bilan bir xil:

```json
{
  "success": true,
  "messages": "Foydalanuvchi logindan muvaffaqiyatli o'tdi",
  "token": "1|tokenstring",
  "is_uzbekistan": true,
  "premium": 0,
  "active_plan": null,
  "data": { "...": "user" }
}
```

---

## 4. `GET /api/v2/check_premium`

Header: `Authorization: Bearer <token>`

⚠️ Premium holati **`"0"` kaliti** ostida keladi (nomsiz array element sifatida yozilgan).

### 3 xil holat

**A) Aktiv premium bor**
```json
{
  "0": true,
  "start_time": "2026-06-01 10:00:00",
  "end_time": "2026-07-01 10:00:00",
  "is_uzbekistan": true,
  "plan_id": 1
}
```

**B) Premium yo'q + O'zbekiston**
```json
{
  "0": false,
  "is_uzbekistan": true
}
```

**C) Premium yo'q + O'zbekiston EMAS** → backend avtomatik 3 kunlik premium beradi (faqat javobda, DB'ga yozilmaydi)
```json
{
  "0": true,
  "is_uzbekistan": false,
  "start_time": "2026-06-04 12:00:00",
  "end_time": "2026-06-07 12:00:00",
  "plan_id": null
}
```

---

## Flutter model'lar

### Login / Exchange token

```dart
class LoginResponse {
  final bool success;
  final String? token;
  final bool isUzbekistan;
  final bool isPremium;
  final Map<String, dynamic>? activePlan;
  final Map<String, dynamic>? user;

  LoginResponse({
    required this.success,
    this.token,
    required this.isUzbekistan,
    required this.isPremium,
    this.activePlan,
    this.user,
  });

  factory LoginResponse.fromJson(Map<String, dynamic> json) => LoginResponse(
        success: json['success'] == true,
        token: json['token'],
        isUzbekistan: json['is_uzbekistan'] == true,
        isPremium: json['premium'] == 1,
        activePlan: json['active_plan'],
        user: json['data'],
      );
}
```

### Google link

```dart
class GoogleLinkResponse {
  final bool isUzbekistan;
  final String googleLink;
  final String telegramLink;

  GoogleLinkResponse({
    required this.isUzbekistan,
    required this.googleLink,
    required this.telegramLink,
  });

  factory GoogleLinkResponse.fromJson(Map<String, dynamic> json) {
    final data = json['data'] as Map<String, dynamic>;
    return GoogleLinkResponse(
      isUzbekistan: data['is_uzbekistan'] == true,
      googleLink: data['google_link'] ?? '',
      telegramLink: data['telegram_link'] ?? '',
    );
  }
}
```

### Check premium

```dart
class CheckPremiumResponse {
  final bool isPremium;
  final bool isUzbekistan;
  final DateTime? startTime;
  final DateTime? endTime;
  final int? planId;

  CheckPremiumResponse({
    required this.isPremium,
    required this.isUzbekistan,
    this.startTime,
    this.endTime,
    this.planId,
  });

  factory CheckPremiumResponse.fromJson(Map<String, dynamic> json) =>
      CheckPremiumResponse(
        // premium holati "0" kaliti ostida keladi
        isPremium: json['0'] == true,
        isUzbekistan: json['is_uzbekistan'] == true,
        startTime: json['start_time'] != null
            ? DateTime.tryParse(json['start_time'].toString())
            : null,
        endTime: json['end_time'] != null
            ? DateTime.tryParse(json['end_time'].toString())
            : null,
        planId: json['plan_id'],
      );
}
```

---

## Flutter'da ishlatish tavsiyasi

```dart
// is_uzbekistan qiymatini global saqlash
class RegionService {
  static bool isUzbekistan = false;
}

// Login'dan keyin
RegionService.isUzbekistan = loginResponse.isUzbekistan;

// App ochilganda / har safar premium tekshirilganda yangilash
final res = await api.checkPremium();
RegionService.isUzbekistan = res.isUzbekistan;

// UI
if (RegionService.isUzbekistan) {
  // To'lov (Click/Payme), plan'lar, promo-kod ekranlarini ko'rsatish
} else {
  // To'lov ekranlarini yashirish — backend 3 kunlik premium beradi
}
```

**Eslatmalar**
- `is_uzbekistan` IP bo'yicha — foydalanuvchi VPN yoqsa o'zgaradi, shuning uchun uni `check_premium` orqali tez-tez yangilab turing, faqat login'dagi qiymatga tayanmang.
- Login'dan oldingi ekranlar (masalan, Google/Telegram tugmalari) uchun `GET /api/v2/google/link` dagi qiymatdan foydalaning.
