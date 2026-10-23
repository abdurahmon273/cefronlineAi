# Full Speaking yuklash muammosi — Flutter uchun (API v2)

## Muammo

Full speaking (yoki Full Mock ichidagi speaking) yuborilganda ilovada uzoq **loading**, keyin **"server error"** chiqadi.

## Backend tomonda tekshirilgan narsalar — muammo yo'q

| Tekshirildi | Natija |
|---|---|
| Bazada natija (`speaking_results`) | **Umuman yozuv yo'q** — hatto chala natija ham yo'q |
| PHP `post_max_size`, `upload_max_filesize`, `max_file_uploads`, `memory_limit` | Yetarli |
| Queue / worker'lar | To'g'ri ishlaydi |
| Foydalanuvchi krediti (`with_ai=1`) | Yetarli |

`save()` metodida natija birinchi qatorlardayoq yaratiladi (validatsiyadan keyin darhol). Bazada hech narsa yo'qligi — **so'rov backend kodiga yetib kelmayapti** degani. Ya'ni so'rov server'ga to'liq yetib kelmasdan ilova tomonida uzilyapti.

> **Xulosa: xato ilova ↔ tarmoq qismida — eng ehtimoli upload timeout.**

---

## Nima uchun faqat Full'da?

Hozirgi yozuvlar ~**129 kbps** (≈ 16 KB/sekund) da yozilyapti. Test holati: part 1 — 6 × 30s, part 2 va part 3 — 2 × 2 min.

| So'rov | Audio | Body hajmi |
|---|---|---|
| Part 1 alohida | 180s | ~2.9 MB |
| Part 2 alohida | 120s | ~1.9 MB |
| Part 3 alohida | 120s | ~1.9 MB |
| **Full (bitta so'rov)** | **420s** | **~6.8 MB** |

Mobil internetda upload tezligi:

| Uplink | 6.8 MB upload vaqti |
|---|---|
| 4 Mbps | ~14 s |
| 1 Mbps | ~55 s |
| 512 kbps | ~110 s |

Dio / `http` paketlaridagi odatiy timeout'lar **30–60 s**. Alohida part'lar vaqtida ulguradi, Full esa ulgurmaydi → ilova so'rovni o'zi uzadi → server hech narsa olmaydi → bazada hech narsa yo'q.

---

## Ilova tomonida tekshirish kerak bo'lgan narsalar

### 1. Timeout'lar (asosiy gumon)

Dio'da `connectTimeout`, `sendTimeout`, `receiveTimeout` global `BaseOptions` da qisqa berilgan bo'lsa, katta upload shu yerda uziladi. Speaking save uchun **alohida, uzun** timeout bering:

```dart
final response = await dio.post(
  '/api/v2/speaking/save',
  data: formData,
  options: Options(
    sendTimeout: const Duration(minutes: 5),     // upload uchun
    receiveTimeout: const Duration(minutes: 2),  // javob uchun
  ),
  onSendProgress: (sent, total) {
    debugPrint('upload: $sent / $total');
  },
);
```

`http` paketi ishlatilsa — `.timeout(...)` qiymatini tekshiring, u butun upload vaqtini ham qamrab oladi.

### 2. Xatoni "server error" deb yashirmaslik

Hozir har qanday `DioException` "server error" deb ko'rsatilyapti shekilli. Haqiqiy turini log qiling — shu bitta ma'lumot muammoni aniq ko'rsatadi:

```dart
} on DioException catch (e) {
  debugPrint('type: ${e.type}');                    // sendTimeout / receiveTimeout / connectionError ...
  debugPrint('status: ${e.response?.statusCode}');  // null bo'lsa — javob umuman kelmagan
  debugPrint('body: ${e.response?.data}');
}
```

| Natija | Ma'nosi |
|---|---|
| `DioExceptionType.sendTimeout` | Upload ulgurmadi → timeout'ni oshiring |
| `DioExceptionType.receiveTimeout` | Upload tugadi, javob kutishda uzildi → backend'ga xabar bering |
| `DioExceptionType.connectionError` | Tarmoq uzildi (masalan, ilova background'ga o'tdi) |
| `statusCode` 4xx/5xx + `body` | So'rov serverga yetib bordi → `body` ni backend'ga yuboring |

### 3. `onSendProgress` bilan tekshirish

- Progress **100% ga yetmasdan** to'xtasa → tarmoq/timeout (ilova tomonida).
- 100% ga yetib, keyin kutib qolsa → backend tomonida, bizga xabar bering.

Foydalanuvchiga ham progress bar ko'rsatish tavsiya qilinadi — 7 MB yuklash sekin internetda bir daqiqa olishi mumkin.

### 4. Fayllarni xotiraga o'qimang

`MultipartFile.fromBytes(await file.readAsBytes())` barcha audioni RAM ga yuklaydi. Diskdan stream qiling:

```dart
await MultipartFile.fromFile(path, filename: 'q_$questionId.webm')
```

### 5. Log interceptor'lar

`LogInterceptor(requestBody: true)`, Alice, Chucker kabi interceptor'lar multipart body'ni to'liq buferlaydi — katta upload'ni sekinlashtiradi yoki xotirani to'ldiradi. Production'da o'chiring yoki `requestBody: false` qiling.

### 6. Ilova background'ga o'tishi

Upload vaqtida ekran o'chsa yoki ilova background'ga o'tsa, OS so'rovni uzishi mumkin. Test qilganda ekranni ochiq holda tuting.

### 7. Audio bitrate'ni kamaytirish (tavsiya)

Nutq uchun 129 kbps shart emas. `record` paketida:

```dart
const RecordConfig(
  encoder: AudioEncoder.opus,  // yoki hozirgi encoder
  bitRate: 32000,              // 32 kbps — nutq uchun yetarli
  sampleRate: 16000,
  numChannels: 1,
)
```

Bunda full speaking **~6.8 MB → ~1.7 MB**, ya'ni 4 barobar tezroq yuklanadi. (Backend baribir audioni 32 kbps mp3 ga o'giradi, sifat yo'qolmaydi.)

> ⚠️ **Format:** backend validatsiyasi `webm, mp3, wav, m4a, ogg` qabul qiladi, lekin hozirda **`.m4a` (AAC) fayllar `mp4` deb aniqlanib rad etilishi mumkin** (400 qaytadi). Backend'da tuzatilguncha hozirgi formatni o'zgartirmang yoki avval bizga ayting.

---

## API shartnomasi

### `POST /api/v2/speaking/save`

Header: `Authorization: Bearer <token>`, `Content-Type: multipart/form-data`

| Maydon | Turi | Izoh |
|---|---|---|
| `category_type` | `1`–`4` | 4 = Full |
| `speaking_exercise_id` | int | |
| `with_ai` | `0` / `1` | |
| `recordings[part_one_first][i][questionId]` | int | |
| `recordings[part_one_first][i][file]` | file | |
| `recordings[part_one_second][i][questionId]` | int | |
| `recordings[part_one_second][i][file]` | file | |
| `recordings[part_two][0][questionId]` | int | bitta |
| `recordings[part_two][0][file]` | file | bitta |
| `recordings[part_three][0][questionId]` | int | bitta |
| `recordings[part_three][0][file]` | file | bitta |

- Har bir fayl: `webm, mp3, wav, m4a, ogg`, maksimal **20 MB**.
- `part_two` va `part_three` da **faqat bitta** yozuv yuboring — backend faqat birinchisini tekshiradi.

Nested `FormData.fromMap` kalit nomlarini boshqacha kodlashi mumkin. Kalitlarni qo'lda yozish ishonchliroq:

```dart
final form = FormData();
form.fields
  ..add(const MapEntry('category_type', '4'))
  ..add(MapEntry('speaking_exercise_id', '$exerciseId'))
  ..add(const MapEntry('with_ai', '1'));

void addPart(String part, List<Recording> items) {
  for (var i = 0; i < items.length; i++) {
    form.fields.add(MapEntry('recordings[$part][$i][questionId]', '${items[i].questionId}'));
    form.files.add(MapEntry(
      'recordings[$part][$i][file]',
      MultipartFile.fromFileSync(items[i].path, filename: 'q_${items[i].questionId}.webm'),
    ));
  }
}

addPart('part_one_first', partOneFirst);
addPart('part_one_second', partOneSecond);
addPart('part_two', partTwo);
addPart('part_three', partThree);
```

**Javob `202`** (200 emas — 2xx ichida, Dio default'da muvaffaqiyatli hisoblaydi):

```json
{
  "id": 91,
  "message": "Speaking Successfully saved.",
  "next_exercise_id": null
}
```

**Xato `400`:**

```json
{
  "errors": ["The given data was invalid."],
  "message": "nimadur xato admin bilan bog`laning"
}
```

### `POST /api/v2/Full/save` (Full Mock)

Speaking qismi `speaking_result` ichida keladi, qolgani bir xil:

```
full_exercise_id
speaking_result[speaking_exercise_id]
speaking_result[with_ai]
speaking_result[recordings][part_one_first][0][questionId]
speaking_result[recordings][part_one_first][0][file]
...
```

Javob `200`: `{ "data": { ... }, "message": "full result saqlandi" }`

Full Mock'da listening/reading/writing ham shu so'rovda ketgani uchun body undan ham katta — timeout tavsiyalari bu yerga **ayniqsa** tegishli.

---

## Save'dan keyin: status polling

`GET /api/v2/speaking/result/status/{id}`

```json
{
  "success": true,
  "data": { "status": 1, "status_text": "Preparing Audios", "percentage": 0, "completed": false }
}
```

| `status` | Ma'nosi |
|---|---|
| 0 | AI'siz saqlandi (`completed: true`) |
| 1 | Audio tayyorlanmoqda |
| 2 | AI tekshirmoqda |
| 3 | Tayyor (`percentage: 100`) |
| 4 | Xato / kredit yetmadi (`completed: true`) |

- `completed == true` bo'lganda polling'ni to'xtating.
- ⚠️ API limiti **daqiqasiga 60 so'rov** (barcha endpoint'lar birga). Full speaking tekshiruvi bir necha daqiqa oladi — har **3–5 sekundda** so'rang, har sekundda emas, aks holda **429** qaytadi.

---

## Qisqa checklist

- [ ] Speaking save uchun `sendTimeout` ≥ 5 min, `receiveTimeout` ≥ 2 min
- [ ] `DioException.type` va `statusCode` ni log qilish, "server error" bilan yashirmaslik
- [ ] `onSendProgress` — progress bar + tekshiruv
- [ ] `MultipartFile.fromFile` (xotiraga o'qimaslik)
- [ ] Production'da log interceptor body'ni yozmasin
- [ ] Bitrate 32 kbps mono (hajm ~4 barobar kam)
- [ ] Polling har 3–5 sekundda, `completed` da to'xtatish

Test natijasini (`e.type`, `statusCode`, progress qayerda to'xtagani) backend'ga yuboring — shunda muammo aniq yopiladi.
