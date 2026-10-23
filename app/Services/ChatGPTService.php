<?php

namespace App\Services;


use App\Models\GlobalSetting;
use App\Models\WritingPrompt;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatGPTService
{
    protected string $openaiUrl;
    protected string $openaiKey;
    protected string $model;
    protected float  $temperature=0.2;
    protected int    $maxTokens;
    protected $globalSetting;

    public function __construct()
    {
        $this->globalSetting=GlobalSetting::orderByDesc('effective_at')->firstOrFail();

        $this->openaiKey = $this->globalSetting->openai_key;
        $this->model = $this->globalSetting->openai_model;
        $this->maxTokens = $this->globalSetting->openai_max_tokens;
        $this->openaiUrl=$this->globalSetting->openai_chat_url;

    }

    public function ResponseByText($instructions,$inputText,$schema,array $context = [])
    {

        $payload = [
            'model' => $this->model,
            'instructions' => $instructions,
            'input' => $inputText,
            'max_output_tokens' =>$this->maxTokens,

            'text' => [
                'format' => [
                    'type'   => 'json_schema',
                    'name'   => 'cefr_writing_score',
                    'strict' => true,
                    'schema' => $schema,
                ],
            ],
        ];

        // ---- DIAGNOSTIKA (mavjud logikaga tegmaydi) ----
        $diagRequestId = $context['request_id'] ?? (string) Str::uuid();
        $diagUserId    = $context['user_id'] ?? null;
        $diagJobId     = $context['job_id'] ?? null;
        $diagSchemaBytes  = strlen(json_encode($schema));
        $diagPayloadBytes = strlen(json_encode($payload));
        $diagStartedAt = microtime(true);

//        Log::channel('telegram')->error(
//            "OPENAI REQUEST STARTED"
//            ."\nrequest_id: {$diagRequestId}"
//            ."\nuser_id: {$diagUserId}"
//            ."\njob_id: {$diagJobId}"
//            ."\nmodel: {$this->model}"
//            ."\nurl: {$this->openaiUrl}"
//            ."\nmax_output_tokens: {$this->maxTokens}"
//            ."\ninstructions_bytes: ".strlen($instructions)
//            ."\ninput_bytes: ".strlen($inputText)
//            ."\nschema_bytes: {$diagSchemaBytes}"
//            ."\npayload_bytes: {$diagPayloadBytes}"
//            ."\nstarted_at: ".now()->toDateTimeString()
//        );
        // ---- /DIAGNOSTIKA ----

        try {
        $response = Http::withToken($this->openaiKey)
            ->acceptJson()
            ->connectTimeout(10)     // TCP ulanish uchun
            ->timeout(180)           // umumiy kutish vaqti (sekund)
            ->retry(2, 500)          // 2 marta qayta urinsin, 500ms pauza
            ->post($this->openaiUrl, $payload);
        } catch (RequestException $e) {
            // ---- DIAGNOSTIKA: 520/502/503/504/429/401 kabi xatolarni aniqlash ----
            Log::channel('telegram')->error(
                "OPENAI REQUEST FAILED"
                ."\nrequest_id: {$diagRequestId}"
                ."\nuser_id: {$diagUserId}"
                ."\njob_id: {$diagJobId}"
                ."\nstatus: ".$e->response?->status()
                ."\nduration_ms: ".round((microtime(true) - $diagStartedAt) * 1000)
                ."\nopenai_request_id: ".$e->response?->header('x-request-id')
                ."\ncf_ray: ".$e->response?->header('cf-ray')
                ."\nmodel: {$this->model}"
                ."\nurl: {$this->openaiUrl}"
                ."\ninstructions_bytes: ".strlen($instructions)
                ."\ninput_bytes: ".strlen($inputText)
                ."\nschema_bytes: {$diagSchemaBytes}"
                ."\npayload_bytes: {$diagPayloadBytes}"
                ."\nexception: ".$e->getMessage()
                ."\nresponse_body: ".mb_substr((string) $e->response?->body(), 0, 1000)
            );

            throw $e;
        }

        // ---- DIAGNOSTIKA: muvaffaqiyatli request ----
//        Log::channel('telegram')->error(
//            "OPENAI REQUEST SUCCESS"
//            ."\nrequest_id: {$diagRequestId}"
//            ."\nuser_id: {$diagUserId}"
//            ."\njob_id: {$diagJobId}"
//            ."\nstatus: ".$response->status()
//            ."\nduration_ms: ".round((microtime(true) - $diagStartedAt) * 1000)
//            ."\nopenai_request_id: ".$response->header('x-request-id')
//            ."\ncf_ray: ".$response->header('cf-ray')
//            ."\nmodel: {$this->model}"
//            ."\nresponse_bytes: ".strlen($response->body())
//            ."\nusage: ".json_encode($response->json('usage', []))
//        );
        // ---- /DIAGNOSTIKA ----

        $response_json   = (array) $response->json();

        $usageData = $response_json['usage'] ?? [];

        $usage = [
            'input'  => $usageData['input_tokens']  ?? 0,
            'output' => $usageData['output_tokens'] ?? 0,
            'total'  => $usageData['total_tokens']  ?? 0,
        ];

        return [
            'result_json' => $this->extractJsonText($response_json),
            'usage'=>$usage,
        ];
    }

    public function extractJsonText(array $response): ?array
    {
        foreach ($response['output'] ?? [] as $item) {

            if (($item['type'] ?? null) !== 'message') {
                continue;
            }

            foreach ($item['content'] ?? [] as $content) {

                if (($content['type'] ?? null) === 'output_text') {

                    $decoded = json_decode($content['text'], true);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $decoded;
                    }
                }
            }
        }

        return null;
    }

}
