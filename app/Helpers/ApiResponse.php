<?php

namespace App\Helpers;

class ApiResponse
{
    /**
     * Generate a standardized not found response.
     *
     * @param string $resource The name of the resource that was not found.
     * @return \Illuminate\Http\JsonResponse
     */
    public static function notFound($resource = 'Resource')
    {
        return response()->json([
            'code'    => 404,
            'success' => false,
            'message' => 'validation errors',
            'errors'  => [strtolower($resource) => "$resource not found"]
        ], 404);
    }

    /**
     * Generate a standardized bad request response.
     *
     * @param string $message
     * @param array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public static function badRequest($message = 'bad request', $errors = [])
    {
        return response()->json([
            'code'    => 400,
            'success' => false,
            'message' => $message,
            'errors'  => $errors
        ], 400);
    }

    /**
     * Generate a standardized internal server error response.
     *
     * @param string $message
     * @param array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public static function serverError($message = 'internal server error', $errors = [])
    {
        return response()->json([
            'code'    => 500,
            'success' => false,
            'message' => $message,
            'errors'  => $errors
        ], 500);
    }
}
