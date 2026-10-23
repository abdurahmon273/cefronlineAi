<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AudioController extends Controller
{
    public function speakingStream($filename)
    {
        $path = "speaking/{$filename}";
        if (!Storage::disk('private')->exists($path)) {
            abort(404);
        }

        return new StreamedResponse(function () use ($path) {
            $stream = Storage::disk('private')->readStream($path);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => 'audio/mpeg',
            'Content-Disposition' => 'inline; filename="audio.mp3"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
    public function speakingResultStream($filename)
    {
        $path = "voiceAudios/{$filename}";
        if (!Storage::disk('private')->exists($path)) {
            abort(404);
        }

        return new StreamedResponse(function () use ($path) {
            $stream = Storage::disk('private')->readStream($path);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => 'audio/mpeg',
            'Content-Disposition' => 'inline; filename="audio.mp3"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
