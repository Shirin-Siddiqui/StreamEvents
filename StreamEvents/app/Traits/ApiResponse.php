<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Returns success json response
     * @param array $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function success(array $data = [], string $message = '', int $code = 200): JsonResponse
    {
        return $this->response('success', $code, $message, $data);
    }

    /**
     * Returns error json response
     * @param array $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function error(array $data = [], string $message = '', int $code = 500): JsonResponse
    {
        return $this->response('error', $code, $message, $data);
    }

    /**
     * Returns json response
     * @param string $status
     * @param int $code
     * @param string $message
     * @param array $data
     * @return JsonResponse
     */
    protected function response(string $status, int $code, string $message = '', array $data = []): JsonResponse
    {
        $response = [
            'status' => $status
        ];

        if($message) {
            $response['message'] = $message;
        }

        if($data) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }
}
