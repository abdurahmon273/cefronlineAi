# AI yordamchi uchun qoidalar (Claude Code / Codex)

## Foydalanuvchi haqida
- Saytni ochish uchun HAR DOIM aynan shu manzilni ber: **http://127.0.0.1:8000/** va "Chrome'da oching" deb ayt. `localhost` so'zini ishlatma.
- Foydalanuvchi **dasturchi emas**, kodni tushunmaydi.
- Har doim **o'zbek tilida**, oddiy so'zlar bilan javob ber. Texnik atamani ishlatsang, qisqacha tushuntir.
- Kodni ko'rsatib tushuntirma. Nima o'zgarganini va saytning **qaysi sahifasida** ko'rinishini ayt.
- Javob qisqa bo'lsin: nima qilding, natijani qayerda ko'rish mumkin, foydalanuvchi nimani tekshirishi kerak.
- So'rov noaniq bo'lsa (masalan "chiroyli qil"), o'zgartirishdan oldin 1-2 ta oddiy savol berib aniqlab ol.

## Loyiha
- Laravel 10 (PHP 8.2), MySQL, Livewire, Blade, Vite. Windows + Laragon.
- Papka: `C:\laragon\www\cefronlineAi`
- Sayt faqat localda ishlaydi: http://127.0.0.1:8000/
- Saytning ikki tomoni bor:
  - **user tomoni**: oddiy Chrome oynasida tekshiriladi;
  - **admin tomoni**: Chrome Incognito oynasida tekshiriladi.
- To'liq o'rnatish qo'llanmasi: `SETUP_WINDOWS.md`

## Saytni ishga tushirish
Foydalanuvchi "ishga tushir" / "run qil" / "sayt ochilmayapti" desa, quyidagi tartibda o'zing bajar (PowerShell):

1. MySQL ishlayaptimi, tekshir:
   ```
   Get-Process mysqld -ErrorAction SilentlyContinue
   ```
   Ishlamayotgan bo'lsa, Laragon'ning MySQL'ini ishga tushir:
   ```
   $mysqld = Get-ChildItem C:\laragon\bin\mysql\*\bin\mysqld.exe | Select-Object -First 1
   Start-Process $mysqld.FullName -ArgumentList "--defaults-file=$(Split-Path (Split-Path $mysqld.FullName))\my.ini" -WindowStyle Hidden
   ```
   Bu ishlamasa, foydalanuvchiga ayt: "Laragon dasturini ochib **Start All** tugmasini bosing."
2. 8000-port bandmi, tekshir: `netstat -ano | findstr :8000`. Band bo'lsa, server allaqachon ishlayapti, qayta yoqma.
3. Serverni **fonda** (background) ishga tushir: `php artisan serve --port=8000`
4. Frontend fayllari (css/js, `resources/js`, `resources/css`, blade'dagi tailwind class'lar) o'zgargan bo'lsa: `npm run build`
5. `.env` yoki `config/` o'zgargan bo'lsa: `php artisan optimize:clear`
6. Oxirida foydalanuvchiga ayt: "Chrome'da http://127.0.0.1:8000/ ni oching (yoki F5 bosing)."

`php` topilmasa, `C:\laragon\bin\php\php-8.2*\` ichidagi `php.exe` ni to'liq yo'li bilan ishlat.

## Xavfsizlik qoidalari (MAJBURIY)
1. **Har bir o'zgarishdan oldin** ishlayotgan holatni saqla:
   ```
   git add -A
   git commit -m "backup: <qisqa tavsif>"
   ```
   O'zgarish ishlagandan keyin yana commit qil (tavsifi o'zbekcha va tushunarli bo'lsin).
2. Foydalanuvchi "buzildi", "orqaga qaytar", "oldingi holatga qaytar" desa: oxirgi commit'dan keyingi o'zgarishlarni bekor qil (`git restore .` va `git clean -fd` bilan, faqat `storage/` va `.env` ga tegmasdan). Undan oldingi holat kerak bo'lsa, `git log` dan tanlab, foydalanuvchiga oddiy tilda tasdiqlat.
3. **Bazani o'chiradigan yoki tozalaydigan buyruqlarni HECH QACHON ishlatma:** `migrate:fresh`, `migrate:reset`, `migrate:rollback`, `db:wipe`, `DROP`, `TRUNCATE`, jadvaldan ommaviy `DELETE`. Seeder'larni ham ishga tushirma.
   Yangi jadval yoki ustun kerak bo'lsa, faqat **yangi migration** yarat va `php artisan migrate` qil.
4. **`.env` fayliga tegma:** o'qima, ko'rsatma, o'zgartirma. Unda parol va kalitlar bor. Yangi sozlama kerak bo'lsa, `.env.example` ga qo'sh va foydalanuvchiga `.env` ga nima yozishni ayt.
5. **`storage/app` ichidagi fayllarni o'chirma** (listening/speaking audiolari, rasmlar).
6. **`git push` qilma**, foydalanuvchi aniq so'ramaguncha. Push'dan oldin nima yuborilishini oddiy tilda ayt.
7. `vendor/`, `node_modules/`, `public/build/` papkalaridagi fayllarni qo'lda o'zgartirma.
8. Kalit, token yoki parolni kod ichiga yozma. Faqat `env()` orqali o'qi.
9. Katta o'zgarish (ko'p fayl, dizaynni to'liq qayta qilish, login/to'lov/AI tekshiruv logikasi) bo'lsa, avval rejani 3-5 ta oddiy gapda tushuntir va ruxsat so'ra.

## O'zgarishdan keyin
- Sayt ishlayotganini o'zing tekshir: `php artisan route:list` xatosiz chiqsin. Kerak bo'lsa `curl -s -o NUL -w "%{http_code}" http://127.0.0.1:8000/` qilib ko'r, 200 yoki 302 bo'lishi kerak.
- Xato bo'lsa, `storage/logs/laravel.log` ning oxirini o'qib, tuzat.
- Foydalanuvchiga quyidagilarni ayt:
  - nima o'zgardi (1-3 gap);
  - qaysi sahifada ko'rish mumkin (to'liq manzil, masalan `http://127.0.0.1:8000/admin/...`);
  - user tomonida yoki admin tomonida (Incognito) tekshirish kerakmi.
