# Telegram login — botdan ilovaga qaytish (iOS + Android)

**Sana:** 2026-07-27
**Kimga:** Backend / Telegram bot developer
**Mavzu:** Foydalanuvchi Telegram bot orqali login qilgach ilovaga qanday qaytariladi

---

## 1. Muammo (TL;DR)

Google login brauzerda tugab, backend Universal Link'ga redirect qilgani uchun ilova
o'z-o'zidan ochiladi. **Telegram esa boshqacha:** foydalanuvchi Telegram *ilovasida*
(bot ichida) turadi, redirect yo'q. Bot login tugagach foydalanuvchiga qaytish
**tugmasi/havolasini** o'zi yuborishi kerak.

Qo'shimcha muammo — **iOS'da** Telegram havolalarni ko'pincha o'z **ichki brauzerida**
(in-app browser) ochadi, va u yerda Universal Link ilovaga **ishonchsiz** qaytadi
(ba'zan umuman qaytmaydi).

**Yechim:** bot qaytish tugmasini **custom URL scheme** bilan yuborsin:

```
tmschool://auth-callback?login_token=<64 belgili bir martalik token>
```

Bu iOS/Android'da ilovani **to'g'ridan-to'g'ri**, Telegram ichki brauzerini chetlab
ochadi. Ilova tomoni bunga **allaqachon tayyor** — qo'shimcha mobil ish shart emas.

---

## 2. Muhim: iOS o'z-o'zidan qaytarmaydi

iOS hech qachon avtomatik ilovaga sakramaydi. Bot foydalanuvchiga **bosiladigan**
biror narsa (tugma yoki havola) ko'rsatishi shart. Bu Telegram bot oqimining tabiati,
mobil kod buni o'zgartira olmaydi.

---

## 3. Bot nima yuborishi kerak

Login muvaffaqiyatli bo'lgach, bir martalik `login_token` yaratib, foydalanuvchiga
quyidagilardan birini yuboring (afzal tartibda):

### 3.1. Eng ishonchli — matn ichida custom scheme havola

```
✅ Kirish tasdiqlandi!
Ilovaga qaytish 👉 tmschool://auth-callback?login_token=abc123...
```

> Telegram inline "url" tugmalari ko'pincha faqat `http(s)` va `tg://` schemelarini
> qabul qiladi. Shuning uchun custom scheme'ni **oddiy matn havola** sifatida yuborish
> eng ishonchli — foydalanuvchi bossa iOS/Android ilovani to'g'ridan-to'g'ri ochadi.

### 3.2. Zaxira — Universal Link (inline tugma yoki matn)

```
tmschool ilovada ochish 👉 https://teacher-muzaffar.com/auth-callback?login_token=abc123...
```

Bu Google bilan bir xil Universal Link. iOS'da Telegram ichki brauzerida ishonchsiz,
lekin Android'da va tashqi brauzerda ishlaydi. **Ikkalasini birga yuborsangiz** yaxshi:
asosiysi — custom scheme, zaxira — universal link.

> **Tavsiya:** ikkala havolani ham bitta xabarda bering. Custom scheme ishlamasa
> (masalan eski build), foydalanuvchi universal linkni bosadi.

---

## 4. Ilova nima qiladi (o'zgarmaydi — tayyor)

1. Foydalanuvchi `tmschool://auth-callback?login_token=...` (yoki universal link)ni bosadi.
2. iOS/Android ilovani ochadi, `app_links` paketi URI'ni ushlaydi.
3. Ilova `login_token`ni ajratib oladi va o'zi almashtiradi:
   ```
   POST /api/v2/auth/exchange-token
   { "login_token": "...", "device_id": "..." }
   ```
4. Backend Sanctum `token` qaytaradi (`/v3/login` formatida) — foydalanuvchi kirdi.

Ya'ni **exchange oqimi Google bilan 100% bir xil.** Bot faqat to'g'ri havolani
yuborishi kifoya.

---

## 5. Qat'iy qiymatlar

| Narsa | Qiymat |
|---|---|
| Custom scheme (asosiy) | `tmschool://auth-callback?login_token=<token>` |
| Universal Link (zaxira) | `https://teacher-muzaffar.com/auth-callback?login_token=<token>` |
| Callback "host/path" | `auth-callback` |
| Query parametri | `login_token` (aniq shu nom) |
| Exchange endpoint | `POST /api/v2/auth/exchange-token` |
| Exchange body | `{ "login_token": "...", "device_id": "..." }` |
| Token amri | Bir martalik, ~10 daqiqa |

> ⚠️ Query parametri nomi **aynan `login_token`** bo'lishi shart — ilova shu nomni
> qidiradi. `token`, `code`, `auth_token` emas.

---

## 6. Bir martalik token — muhim

- Token **bir martalik**: ilova uni bir marta `exchange-token`ga yuborgach backend
  o'chiradi/ishlatilgan deb belgilaydi.
- Ikkinchi urinishda backend **422** qaytaradi (`{ "success": false, "errors": "..." }`).
- Ilova endi bir xil tokenni **ikki marta yubormaydi** (token darajasida dedup qo'yilgan),
  shuning uchun bot **har login uchun yangi token** yaratsa yetarli.
- 422 javobida haqiqiy sababni **`errors`** maydonida bering (ilova avval `errors`,
  keyin `message`ni o'qiydi).

---

## 7. Sinash

1. Bot login tugagach `tmschool://auth-callback?login_token=...` yuborsin.
2. **iOS:** ilovani App Store/TestFlight build sifatida (yangi Info.plist bilan)
   qayta o'rnating → Telegram'dagi havolani bosing → ilova ochilib, kirishi kerak.
   > Custom scheme faqat qayta o'rnatilgan build'da ro'yxatdan o'tadi.
3. **Android:** yangi build o'rnatgach havolani bosing → ilova ochilishi kerak.
4. Universal link zaxirasini ham sinang (`https://teacher-muzaffar.com/auth-callback?...`).

---

## 8. Nega custom scheme kerak (qisqacha texnik izoh)

| Bot nima yuboradi | iOS natija | Android natija |
|---|---|---|
| Inline "url" tugma (universal link) | Telegram ichki brauzer → ilovaga **ko'pincha qaytmaydi** ❌ | Odatda ishlaydi ✅ |
| Matn havola (universal link) | Tizim ochadi → ishlaydi, lekin user sozlamasiga bog'liq ⚠️ | Ishlaydi ✅ |
| **Custom scheme** (`tmschool://...`) | Ilovani **to'g'ridan-to'g'ri** ochadi ✅✅ | To'g'ridan-to'g'ri ochadi ✅✅ |

Custom scheme domen egaligini tekshirmaydi (Universal Link/App Link'dan farqli), shuning
uchun Telegram ichki brauzerini chetlab, ilovani darhol ochadi.
