# cefronlineAi — Windows'da ishga tushirish

Bu qo'llanmada PHP, Composer, Node.js, MySQL (Laragon), Git va VS Code allaqachon o'rnatilgan, loyiha esa `git clone` / `git pull` qilingan deb hisoblanadi.

Loyiha papkasi: `C:\laragon\www\cefronlineAi`

> Buyruqlarni **Laragon Terminal** yoki **VS Code terminali** (`Ctrl` + `` ` ``) da yozing.
> SQL import (`<` belgisi) faqat **Laragon Terminal** yoki **cmd** da ishlaydi, PowerShell'da ishlamaydi.

---

## 1. Kerakli fayllar (Telegram'dan yuklab olinadi)

| Fayl | Nima |
|---|---|
| `database.sql` | Baza |
| `storage_admin.zip` | Listening/speaking audiolari, rasmlar (admin kontenti) |
| `env.txt` | `.env` sozlamalari |

Yuklangan fayllar odatda shu papkada bo'ladi: `C:\Users\<USER>\Downloads\Telegram Desktop\`

Quyidagi buyruqlarda shu yo'l `%USERPROFILE%\Downloads\Telegram Desktop` deb yozilgan. Fayllar boshqa joyda bo'lsa, yo'lni o'zgartiring.

---

## 2. Loyihani yangilash

```
cd C:\laragon\www\cefronlineAi
git pull
```

---

## 3. `.env` faylini sozlash

```
copy "%USERPROFILE%\Downloads\Telegram Desktop\env.txt" .env
notepad .env
```

Notepad'da quyidagi qatorlarni tekshirib, shunday qiling:

```
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=teacher_production_1
DB_USERNAME=root
DB_PASSWORD=

FFMPEG_PATH="C:\ffmpeg\bin\ffmpeg.exe"
FFPROBE_PATH="C:\ffmpeg\bin\ffprobe.exe"

OPENAI_API_KEY=sk-...
TELEGRAM_BOT_API_KEY=yangi_log_bot_tokeni
TELEGRAM_LOG_CHAT_ID=chat_id
TELEGRAM_LOG_CHAT_ID_2=
```

- FFmpeg yo'lini bilish uchun terminalda `where ffmpeg` va `where ffprobe` buyrug'ini yozing, chiqqan yo'lni qo'ying.
- Laragon'da MySQL paroli bo'sh, shuning uchun `DB_PASSWORD=` bo'sh qoladi.

`Ctrl` + `S` bosib saqlang.

> `.env` ichida `APP_KEY` bo'sh bo'lsa: `php artisan key:generate`

---

## 4. Paketlarni o'rnatish

```
composer install
npm install
npm run build
```

---

## 5. Bazani yuklash (SQL import)

Avval Laragon oynasida **Start All** bosing (MySQL yoqiladi).

**5.1. Bo'sh baza yaratish:**
```
mysql -u root -e "CREATE DATABASE IF NOT EXISTS teacher_production_1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

**5.2. SQL faylni import qilish** (faqat Laragon Terminal yoki cmd'da):
```
mysql -u root --default-character-set=utf8mb4 teacher_production_1 < "%USERPROFILE%\Downloads\Telegram Desktop\database.sql"
```
Hech narsa chiqmasa, import muvaffaqiyatli bo'ldi. Katta faylda bir necha daqiqa kuting.

**PowerShell'da bo'lsangiz**, shu ko'rinishda yozing:
```
cmd /c "mysql -u root --default-character-set=utf8mb4 teacher_production_1 < \"%USERPROFILE%\Downloads\Telegram Desktop\database.sql\""
```

**Grafik usul:** Laragon → **Database** (HeidiSQL ochiladi) → chapdan `teacher_production_1` ni tanlang → **File → Run SQL file…** → `database.sql`.

**5.3. Bazani qaytadan yuklash kerak bo'lsa** (eski ma'lumot o'chadi):
```
mysql -u root -e "DROP DATABASE teacher_production_1; CREATE DATABASE teacher_production_1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```
Keyin 5.2-qadamni qayta bajaring.

**5.4. Yangi migratsiyalarni qo'llash:**
```
php artisan migrate --force
```

---

## 6. Storage'ni yuklash

Zip ichida `public\...` va `private\...` papkalari bor. Ular `storage\app` ichiga ochiladi:

```
powershell -Command "Expand-Archive -Path \"$env:USERPROFILE\Downloads\Telegram Desktop\storage_admin.zip\" -DestinationPath storage\app -Force"
```

> Buyruqni **PowerShell'ning o'zida** yozsangiz, shu qisqa variant yetarli:
> `Expand-Archive -Path "$env:USERPROFILE\Downloads\Telegram Desktop\storage_admin.zip" -DestinationPath storage\app -Force`

To'g'ri ochilganini tekshiring. Bu papkalar bo'lishi kerak:
```
dir storage\app\public
dir storage\app\private
```
`public` ichida: `audios`, `maps`, `speaking_images`, `stepPages`, raqamli papkalar.
`private` ichida: `speaking`, `speakingInstruction`.

> ⚠️ Agar `storage\app\storage\app\public` kabi ikki marta ichma-ich papka paydo bo'lsa, zip boshqacha yig'ilgan bo'ladi. `public` va `private` papkalarini qo'lda `storage\app` ga ko'chiring.

**Storage link yaratish** (rasmlar va audiolar brauzerda chiqishi uchun):
```
php artisan storage:link
```
"The [public/storage] link already exists" desa, link allaqachon bor, hammasi joyida.

---

## 7. Keshni tozalash

```
php artisan optimize:clear
```

---

## 8. Loyihani ishga tushirish

**1-terminal**, server:
```
php artisan serve --port=8000
```

**2-terminal**, frontend o'zgarishlarini darhol ko'rish uchun (VS Code terminal panelidagi `+` tugmasi):
```
npm run dev
```

**3-terminal**, queue worker (AI tekshiruvi va speaking baholash job'lari uchun; `.env` da `QUEUE_CONNECTION=database` yoki `redis` bo'lsa kerak):
```
php artisan queue:work
```

Brauzerda oching: **http://127.0.0.1:8000/**

Terminallarni yopmang. Yopilsa, sayt to'xtaydi.

---

## 9. Har kuni ishga tushirish (qisqa)

1. Laragon → **Start All**
2. VS Code → `C:\laragon\www\cefronlineAi` ni oching
3. Terminal:
   ```
   git pull
   php artisan migrate --force
   php artisan serve --port=8000
   ```
4. Ikkinchi terminal: `npm run dev`
5. Brauzer: http://127.0.0.1:8000/

---

## 10. Tekshirish ro'yxati

- [ ] `http://127.0.0.1:8000/` ochiladi
- [ ] Login ishlaydi (baza ulangan)
- [ ] Listening testida audio eshitiladi (`public/audios`)
- [ ] Speaking'da rasmlar chiqadi (`public/speaking_images`)
- [ ] Admin panelda speaking audiolari ochiladi (`private/speaking`)
- [ ] Kurs/yangilik rasmlari chiqadi (raqamli papkalar)

---

## 11. Muammolar

| Xato | Yechim |
|---|---|
| `SQLSTATE[HY000] [2002] No connection` | Laragon'da **Start All** bosing |
| `Access denied for user 'root'` | `.env` da `DB_PASSWORD=` bo'sh bo'lsin, keyin `php artisan config:clear` |
| `Unknown database 'teacher_production_1'` | 5.1-qadamni bajaring |
| `The "<" operator is reserved` | Siz PowerShell'dasiz: Laragon Terminal'dan foydalaning yoki `cmd /c "..."` variantini yozing |
| Import'da `ERROR 1067` / `Invalid default value` | Laragon → MySQL → `my.ini` da `sql_mode=""` qiling va MySQL'ni qayta yoqing |
| Rasm/audio 404 | `php artisan storage:link`, keyin 6-qadamdagi papkalarni tekshiring |
| Audio upload'dan keyin ishlamaydi (speaking) | `.env` dagi `FFMPEG_PATH` / `FFPROBE_PATH` to'g'riligini tekshiring |
| `Vite manifest not found` | `npm run build` yoki `npm run dev` |
| `.env` o'zgarishi ta'sir qilmaydi | `php artisan optimize:clear` |
| `Port 8000 is already in use` | Eski `php artisan serve` oynasida `Ctrl` + `C` bosing |
| `ext-... is missing` (composer) | Laragon → PHP → Extensions: `fileinfo`, `gd`, `zip`, `intl`, `pdo_mysql`, `sodium`, `exif` ni yoqing |

---

## 12. Codex bilan ishlash

VS Code'da Codex chatiga topshiriq yozing, masalan:

```
Loyihani http://127.0.0.1:8000/ da ishga tushirishga yordam ber. SETUP_WINDOWS.md dagi qadamlarni tekshir.
```

Kod o'zgargandan keyin: PHP o'zgarishlari darhol ishlaydi. `.env` yoki `config` o'zgarsa, `php artisan optimize:clear` qiling.

O'zgarishlarni GitHub'ga saqlash:
```
git add -A
git commit -m "o'zgarish tavsifi"
git push
```
