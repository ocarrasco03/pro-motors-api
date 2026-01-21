<?php

namespace App\Core\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceResponse;
use Illuminate\Support\Facades\Log;

trait ApiResponse
{
    public function success(mixed $data = null, string $message = 'OK', int $code = 200): JsonResponse
    {
        if ($data instanceof ResourceResponse) {
            $response = $data->toResponse(request())->getData(true);
        } elseif ($data instanceof JsonResource) {
            $response = $data->toArray(request());
            $response = is_array($response) ? $response : ['data' => $response];
        } else {
            if (is_null($data)) {
                $response = [];
            } else {
                $response = ['data' => $data];
            }
        }

        return response()->json(array_merge([
            'message' => $message,
        ], $response), $code);
    }

    public function error(mixed $message = 'Something fails', int $code = 400, mixed $data = null): JsonResponse {
        Log::error($message, [$data]);

        if (is_null($data)) {
            $response = [];
        } else {
            $response = ['data' => $data];
        }

        return response()->json(array_merge([
            'message' => $message,
        ], $response), $code);
    }
}
