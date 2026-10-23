<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class V2ApiController extends Controller
{
    public function success($data, $message, $pagination = null, $code = JsonResponse::HTTP_OK): JsonResponse
    {
        if ($pagination) {
            $meta = [
                'count' => $data->count(),
                'current_page' => $data->currentPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'first_page_url' => $data->url(1),
                'last_page_url' => $data->url($data->lastPage()),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
            ];
            $links = $data->linkCollection();
            $data = $data->getCollection();
            return response()->json([
                'data' => $data,
                'message' => $message,
                'meta' => $meta,
                'links' => $links,
            ]);
        }

        return response()->json([
            'data' => $data,
            'message' => $message,
        ], $code);
    }

    public function error($errors, string $message, int $code = 400): JsonResponse
    {
        return response()->json([
            'errors' => $errors,
            'message' => $message
        ], $code);
    }
}

