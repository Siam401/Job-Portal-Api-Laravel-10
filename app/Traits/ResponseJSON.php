<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

trait ResponseJSON
{
    /**
     * Variable with formatted response array as default
     *
     * @var array
     */
    private $jsonResponse = [
        'success' => false,
        'result_code' => 0,
        'message' => "",
        'data' => []
    ];

    /**
     * Set success response status
     *
     * @return self
     */
    public function success()
    {
        $this->jsonResponse['success'] = true;
        return $this;
    }

    /**
     * Set success response status
     *
     * @return self
     */
    public function resultCode(int $code)
    {
        $this->jsonResponse['result_code'] = $code;
        return $this;
    }

    /**
     * Set response message
     *
     * @param string $message
     * @return self
     */
    public function message(string $message = '')
    {
        $this->jsonResponse['message'] = $message;
        return $this;
    }

    /**
     * Return formatted JSON response
     *
     * @param array|JsonResource|Collection $data
     * @param integer $httpStatus
     * @param string[] $headers
     * @return JsonResponse
     */
    public function response(
        array|JsonResource|Collection $data = [],
        int $httpStatus = 200,
        array $headers = [],
        int $option = 0
    ): JsonResponse {
        $this->jsonResponse['data'] = $data;

        $this->jsonResponse['result_code'] = $this->jsonResponse['result_code'] === 0
            && !$this->jsonResponse['success'] ? 1
            : $this->jsonResponse['result_code'];

        return response()->json(
            $this->jsonResponse,
            $httpStatus,
            $headers,
            $option
        );
    }

    /**
     * Return error response with formatted JSON
     *
     * @param integer $httpStatus
     * @return JsonResponse
     */
    public function error(int $httpStatus = 400)
    {
        return response()->json(
            [
                'success' => false,
                'result_code' => $this->jsonResponse['result_code'] > 0 ? $this->jsonResponse['result_code'] : 1,
                'message' => $this->jsonResponse['message'] ?: 'Something went wrong',
            ],
            $httpStatus,
        );
    }
}