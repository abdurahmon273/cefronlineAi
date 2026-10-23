# V2 Result Status API'lari

Ushbu hujjatda ikki API bayon etilgan: **Writing Result Status** va **Speaking Result Status**. Ikkalasi ham saqlangan natijaning (result) AI tekshiruvi jarayonini / holatini (status) qaytaradi.

## Umumiy ma'lumot

| Xususiyat | Qiymat |
|-----------|--------|
| Base prefix | `/api/v2` |
| Middleware | `auth:sanctum`, `is_verified` |
| Auth | `Bearer <token>` (talab qilinadi) |
| HTTP method | `GET` |
| HTTP status code | Barcha holatda `200` |

---

## 1. Writing Result Status

Yozma (writing) natijasining tekshiruv holatini qaytaradi.

### Endpoint

```
GET /api/v2/writing/result/status/{id}
```

### Path parametr

| Nomi | Turi | Majburiy | Izoh |
|------|------|----------|------|
| `id` | integer | ✅ | `WritingResult` ID si |

### Response — muvaffaqiyatli

```json
{
  "success": true,
  "data": {
    "status": 3,
    "status_text": "Checked",
    "percentage": 100,
    "completed": true
  }
}
```

### Response — natija topilmadi

```json
{
  "success": false,
  "data": null,
  "message": "result not found"
}
```

### `status` qiymatlari (Writing)

| status | status_text |
|--------|-------------|
| 0 | `` (bo'sh) |
| 1 | `Processing` |
| 2 | `Responding` |
| 3 | `Checked` |
| 4 | `Failed` |

> **Eslatma:** `status == 3` bo'lganda API `status_text` sifatida enum qiymatini emas, balki qattiq yozilgan `"Complated"` matnini qaytaradi. `percentage = 100`, `completed = true`.

---

## 2. Speaking Result Status

Og'zaki (speaking) natijasining tekshiruv holatini qaytaradi.

### Endpoint

```
GET /api/v2/speaking/result/status/{id}
```

### Path parametr

| Nomi | Turi | Majburiy | Izoh |
|------|------|----------|------|
| `id` | integer | ✅ | `SpeakingResult` ID si |

### Response — muvaffaqiyatli

```json
{
  "success": true,
  "data": {
    "status": 3,
    "status_text": "Checked Successfully",
    "percentage": 100,
    "completed": true
  }
}
```

### Response — natija topilmadi

```json
{
  "success": false,
  "data": null,
  "message": "result not found"
}
```

### `status` qiymatlari (Speaking)

| status | status_text |
|--------|-------------|
| 0 | `` (bo'sh) |
| 1 | `Preparing Audios` |
| 2 | `Ai Responding` |
| 3 | `Checked Successfully` |
| 4 | `Failed` |

> **Eslatma:** `status == 3` bo'lganda API `status_text` sifatida `"Complated"` matnini qaytaradi (enum qiymati emas).

---

## Response maydonlari (ikkala API uchun umumiy)

| Maydon | Turi | Izoh |
|--------|------|------|
| `success` | boolean | So'rov muvaffaqiyati |
| `data.status` | integer | Holat kodi (yuqoridagi jadvallar) |
| `data.status_text` | string | Holatning matnli ko'rinishi |
| `data.percentage` | integer | Jarayon foizi: `intval(process_category_type / category_type * 100)` |
| `data.completed` | boolean | `status == 4` bo'lganda `true` (yakunlangan) |

### Muhim texnik izohlar

- **AI ishlatilmagan holat (`with_ai = false`):** javob `status: 0`, `status_text: "Complated"`, `percentage: 0`, `completed: true` qaytaradi.
- **percentage hisoblash bo'luvchisi (`category_type`):**
  - Writing: `category_type == '3'` bo'lsa bo'luvchi = `2`, aks holda = `1`.
  - Speaking: `category_type == '4'` bo'lsa bo'luvchi = `3`, aks holda = `1`.
- **Writing xususiyati:** `with_ai = true`, `status != 3` va `status = 0` (falsy) bo'lgan holatda javob bo'sh (`null`) qaytishi mumkin.

## Manba fayllar

| API | Route | Controller |
|-----|-------|------------|
| Writing | `routes/api.php:99` | `app/Http/Controllers/Api/V2/Writing/WritingExerciseApiController.php:816` (`get_result_status`) |
| Speaking | `routes/api.php:112` | `app/Http/Controllers/Api/V2/Speaking/SpeakingExerciseApiController.php:1185` (`get_result_status`) |

Status enum: `app/Enums/CefrTypeEnum.php` (`WRITING_STATUS_DATA`, `SPEAKING_STATUS_DATA`)
