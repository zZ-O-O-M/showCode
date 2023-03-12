<?php

namespace App\Helper;

use App\ToolBox\Data\ApiDataProvider;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ApiResponseGenerator
{
    /**
     * @param ApiDataProvider|array $data
     * @return JsonResponse
     */
    public static function successResponse($data = []): JsonResponse
    {
        if (!empty($data)) {
            if ($data instanceof ApiDataProvider) {
                $data = $data->make();
                if ($data instanceof LengthAwarePaginator) {
                    return self::generatePaginatedResponse($data);
                }
            }
            $data = self::prepareData($data);
        }

        return response()->json(
            [
                'success' => true,
                'data'    => $data,
                'message' => '',
                'errors'  => [],
            ], 200,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param        $errors
     * @param        $status
     * @param string $message
     * @return JsonResponse
     */
    public static function errorResponse($errors, $status, string $message = ''): JsonResponse
    {
        return response()->json(
            [
                'success' => false,
                'data'    => [],
                'message' => $message,
                'errors'  => $errors ?? []
            ], $status,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param        $errors
     * @param string $message
     * @return JsonResponse
     */
    public static function unprocessableEntity($errors = null, string $message = ''): JsonResponse
    {
        return response()->json(
            [
                'success' => false,
                'data'    => [],
                'message' => $message ?? '',
                'errors'  => $errors ?? []
            ], 422,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $message
     * @return JsonResponse
     */
    public static function serverError($message = null): JsonResponse
    {
        return response()->json(
            [
                'success' => false,
                'data'    => [],
                'message' => $message ?? "Something went wrong",
                'errors'  => [],
            ], 500,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $data
     * @return array|mixed
     */
    protected static function prepareData($data)
    {
        $result = $data;
        if ($data instanceof Arrayable) {
            $result = [];

            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $value = self::prepareData($value);
                }
                else if (is_float($value)) {
                    $value = round($value, 2);
                }
                $result[Str::snake($key)] = $value;
            }
        }
        return $result;
    }

    /**
     * @param LengthAwarePaginator $data
     * @return JsonResponse
     */
    protected static function generatePaginatedResponse(LengthAwarePaginator $data): JsonResponse
    {
        $items = $data->items();

        $firstPageUrl   = $data->getUrlRange(1, 1);
        $currentPageUrl = $data->getUrlRange($data->currentPage(), $data->currentPage());
        $lastPageUrl    = $data->getUrlRange($data->lastPage(), $data->lastPage());

        $links = [
            'self'  => array_shift($currentPageUrl),
            'first' => array_shift($firstPageUrl),
            'last'  => array_shift($lastPageUrl),
            'next'  => $data->nextPageUrl(),
            'prev'  => $data->previousPageUrl(),
        ];

        $meta = [
            'total-count'  => $data->total(),
            'page-count'   => $data->lastPage(),
            'current-page' => $data->currentPage(),
            'per-page'     => (int)$data->perPage()
        ];

        return response()->json(
            [
                'success' => true,
                'data'    => self::prepareData($items),
                'links'   => $links,
                'meta'    => $meta,
                'message' => '',
                'errors'  => [],
            ],
            200,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE);
    }
}
