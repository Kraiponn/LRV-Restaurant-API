<?php

namespace App\Traits;

trait ResponseTrait
{
    public function responseSuccess($data,  $statusCode = 200, $message = 'Successfully')
    {
        return response()->json([
            'success' => true,
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data,
            'errors' => null,
        ], $statusCode);
    }

    public function responseError($errors, $statusCode = 403, $message = 'Something went wrong')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ], $statusCode);
    }
}
