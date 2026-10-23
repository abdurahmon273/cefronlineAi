<?php

namespace App\Services;

use App\Traits\BillingTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class DeepgramService
{
    use BillingTrait;
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('deepgram.api_key');
        $this->baseUrl = 'https://api.deepgram.com/v1/speak?model='. config('deepgram.model');
    }

    public function generateAudio(string $text, string $outputPath): array
    {
        $filename = Str::random(32) . '.mp3';
        $fullPath = $outputPath . "/{$filename}";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Token ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['text' => $text]));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        if ($curlError || $httpCode !== 200) {
            curl_close($ch);
            throw new \Exception("Deepgram API error: " . ($curlError ?: "HTTP $httpCode"));
        }

        Storage::disk('private')->put($fullPath, $response);

        curl_close($ch);

        if (!Storage::disk('private')->exists($fullPath)) {
            throw new \Exception("Failed to save audio file to: $fullPath");
        }





        return [
            'audio_name' => $filename,
            'url' => Storage::disk('private')->url($fullPath),
        ];
    }

    /**
     * webm audioni mp3 ga o'giradi (FFmpeg, proc_open orqali — exec ishlatilmaydi).
     * - Muvaffaqiyatli bo'lsa: eski webm faylni o'chiradi va yangi mp3 faylnomini qaytaradi.
     * - Xato bo'lsa: eski webm ni O'CHIRMAYDI, asl faylnomni qaytaradi (webm bilan davom etadi).
     *
     * @param string $filename  voiceAudios ichidagi audio fayl nomi (masalan ex_91_..._.webm)
     * @return string           yangi mp3 nomi yoki (xato bo'lsa) asl nom
     */
    public function generate_mp3(string $filename): string
    {
        $disk = Storage::disk('private');

        // allaqachon mp3 bo'lsa — convert shart emas
        if (Str::endsWith(strtolower($filename), '.mp3')) {
            return $filename;
        }

        $mp3Name    = pathinfo($filename, PATHINFO_FILENAME) . '.mp3';
        $inputPath  = $disk->path("voiceAudios/{$filename}");
        $outputPath = $disk->path("voiceAudios/{$mp3Name}");

        // webm yo'q yoki bo'sh — lekin oldingi konvertatsiyadan mp3 mavjud bo'lsa,
        // mp3 nomini qaytaramiz (retry uchun xavfsiz, idempotent)
        if (!is_file($inputPath) || filesize($inputPath) < 1024) {
            if (is_file($outputPath) && filesize($outputPath) > 0) {
                return $mp3Name;
            }
            return $filename;
        }

        try {
            $process = new Process([
                config('services.ffmpeg.path', 'ffmpeg'),
                '-err_detect', 'ignore_err',   // konteyner header xatolarini kechir
                '-analyzeduration', '20M',     // formatni to'g'ri aniqlash uchun ko'proq tahlil
                '-probesize', '20M',
                '-f', 'webm',                  // formatni majburan webm deb belgila
                '-i', $inputPath,
                '-vn',                         // video oqim bo'lsa — tashla, faqat audio
                '-codec:a', 'libmp3lame',
                '-ac', '1',       // mono — hajm 2x kichik
                '-ar', '22050',   // sample rate past — speech uchun yetarli
                '-b:a', '32k',    // past bitrate — eng kichik hajm (speech uchun ok)
                '-y',             // mavjud faylni almashtir
                $outputPath,
            ]);
            $process->setTimeout(120);
            $process->run();

            if (!$process->isSuccessful() || !is_file($outputPath)) {
                throw new \RuntimeException('ffmpeg convert failed: ' . $process->getErrorOutput());
            }

            // ✅ hammasi yaxshi — mp3 tayyor. Eski webm ni BU YERDA O'CHIRMAYMIZ:
            // avval chaqiruvchi audio_path=mp3 ni DB ga yozadi, keyin webm o'chiriladi.
            return $mp3Name;

        } catch (\Throwable $e) {
            // buzuq/yaroqsiz webm kutilgan holat — faqat local log, Telegram spam yo'q
            Log::warning('generate_mp3 skipped (' . $filename . '): ' . $e->getMessage());

            // ❌ xato — chala yaratilgan mp3 bo'lsa tozalaymiz, ESKI webm ni O'CHIRMAYMIZ
            if (is_file($outputPath)) {
                @unlink($outputPath);
            }

            // asl webm nomini qaytaramiz — downstream webm bilan davom etadi
            return $filename;
        }
    }

    public function generateText(string $filename,$file_type='audio/webm',$globalSetting=null): array
    {

        $url = 'https://api.deepgram.com/v1/listen?model=nova-3&smart_format=true';

        $audioPath = "voiceAudios/$filename";

         $resolvedPath = \Illuminate\Support\Facades\Storage::disk('private')->path($audioPath);

        if (!is_file($resolvedPath) ) {
            throw new \InvalidArgumentException("Audio file not found or empty: {$audioPath}");
        }

        $binary = file_get_contents($resolvedPath);
        if ($binary === false) {
            throw new \RuntimeException("Unable to read audio file: {$resolvedPath}");
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        $mime = Storage::disk('private')->mimeType($audioPath) ?? 'application/octet-stream';

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Token ' . $this->apiKey,
            'Content-Type: ' . $mime,
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $binary);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || $httpCode < 200 || $httpCode >= 300) {
            throw new \Exception("Deepgram STT error: " . ($curlError ?: "HTTP $httpCode: $response"));
        }

        $json = json_decode($response, true);
        if (!is_array($json)) {
            throw new \Exception("Deepgram STT parse error: invalid JSON");
        }

        // Oddiy transcriptni ajratib olish (Deepgram standard javobi)
        $text = $json['results']['channels'][0]['alternatives'][0]['transcript'] ?? '';

        $duration_s = (float)($json['metadata']['duration'] ?? 0.0);

//        $ai_price = $this->FindDeepGramAudioToTextPrice($globalSetting, $duration_s);


        return [
            'text' => $text,
            'price'=>0
        ];
    }
    public function generateText1(string $filename, $file_type='audio/webm', $globalSetting=null): array
    {
        $url = 'https://api.deepgram.com/v1/listen?model=nova-3&smart_format=true';
        $audioPath = "voiceAudios/$filename";
        $resolvedPath = Storage::disk('private')->path($audioPath);

        if (!is_file($resolvedPath)) {
            throw new \InvalidArgumentException("Audio file not found: {$audioPath}");
        }

        $binary = file_get_contents($resolvedPath);
        if ($binary === false) {
            throw new \RuntimeException("Unable to read audio file: {$resolvedPath}");
        }

        $mime = Storage::disk('private')->mimeType($audioPath) ?? 'application/octet-stream';

        $maxAttempts = 3;
        $lastError = null;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Token ' . $this->apiKey,
                    'Content-Type: ' . $mime,
                    'Accept: application/json',
                ],
                CURLOPT_POSTFIELDS => $binary,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 120,
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ]);

            $response  = curl_exec($ch);
            $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErrNo = curl_errno($ch);
            $curlError = curl_error($ch);

            curl_close($ch);

            if (!$curlErrNo && $httpCode >= 200 && $httpCode < 300) {
                $json = json_decode($response, true);

                if (!is_array($json)) {
                    throw new \Exception("Deepgram STT parse error: invalid JSON");
                }

                $text = $json['results']['channels'][0]['alternatives'][0]['transcript'] ?? '';

                return [
                    'text' => $text,
                    'price' => 0,
                ];
            }

            $lastError = "attempt={$attempt}, curl_errno={$curlErrNo}, error=" . ($curlError ?: "HTTP {$httpCode}: {$response}");

            if ($attempt < $maxAttempts) {
                usleep(700000); // 0.7 sec
            }
        }

        throw new \Exception("Deepgram STT error: {$lastError}");
    }
}
