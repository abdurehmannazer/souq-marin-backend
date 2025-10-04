<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    /**
     * Send a standardized JSON response.
     *
     * @param bool $success         Indicates if the request was successful
     * @param string $message       Response message
     * @param array $data           Optional response data
     * @param (int|null) $statusCode       HTTP status code (default 599 for errors)
     * @return JsonResponse
     */
    public function BaseResponse(bool $success,string $message,$data = [],int $statusCode = 201 ): JsonResponse
    {
        // If success, override status code to 201 (Created)
        $statusCode = $success ? 201 : $statusCode;

        $response = [
            'typeResponse' => $success,
            'message'      => $message,
            'data'         => $data,
        ];

        return response()->json($response, $statusCode);
    }
}
