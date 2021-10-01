<?php


namespace App\Traits;


use Illuminate\Http\JsonResponse;

trait ResponseGenerator
{
    /**
     * @param array $data
     * @param int $code
     * @return JsonResponse
     */
    protected function successResponse($data = [], $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'payload' => $data
        ], $code);
    }

    /**
     * @param string|null $message
     * @param array $data
     * @param int $code
     * @return JsonResponse
     */
    protected function errorResponse($message = null, $data = [], $code = 500)
    {
        $response = [
            'status' => 'failure',
            'message' => $message
        ];

        if ($data) {
            $response['payload'] = $data;
        }

        return response()->json($response, $code);
    }
}
