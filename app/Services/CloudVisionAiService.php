<?php

namespace App\Services;

use App\Models\GlobalSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class CloudVisionAiService
{
    protected string $cloud_vision_key;
    protected $globalSetting;

    public function __construct()
    {
        $this->globalSetting = GlobalSetting::orderByDesc('effective_at')->firstOrFail();
        $this->cloud_vision_key = $this->globalSetting->cloud_vision_key;
    }

    public function ImagesToText(array $images): array
    {
        $results = [];

        foreach ($images as $index => $image) {
            try {
                $response=$this->ImageToText($image);

            }catch (Exception $e) {
                $response=' ';
            }

            if ($response){
                $results[$index] = $this->format_text($response);

            }else{
                $results[$index] = '';

            }
        }

        return $results;
    }

    public function format_text($text): string
    {
        $text = str_replace(["\r\n", "\r", "\n"], ' ', $text);

        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text) . "\n";
    }

    public function ImageToText($image): string
    {
        try {
            $imagePath = $this->getImagePath($image);

            if (!file_exists($imagePath)) {
                throw new Exception('Image file not found');
            }

            $base64Image = base64_encode(file_get_contents($imagePath));

            $response = Http::timeout(60)->post(
                'https://vision.googleapis.com/v1/images:annotate?key=' . $this->cloud_vision_key,
                [
                    'requests' => [
                        [
                            'image' => [
                                'content' => $base64Image,
                            ],
                            'features' => [
                                [
                                    'type' => 'DOCUMENT_TEXT_DETECTION',
                                ],
                            ],
                        ],
                    ],
                ]
            );

            if (!$response->successful()) {
                Log::error('Google Vision OCR Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new Exception('Google Vision OCR failed');
            }

            return data_get($response->json(), 'responses.0.fullTextAnnotation.text', '');

        } catch (Exception $e) {
            Log::error('CloudVisionAiService ImageToText Error', [
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function getImagePath($image): string
    {
        if ($image instanceof UploadedFile) {
            return $image->getRealPath();
        }

        if (is_string($image)) {
            return $image;
        }

        throw new Exception('Invalid image format');
    }
}
