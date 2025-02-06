<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function responseJson($data, $message = '', $statusCode = 200)
    {
        $payload =  [
            'data' => $data ?? [],
            'message' => $message,
            'status' => in_array($statusCode, [200, 201]) ? true : false
        ];

        return response()->json($payload, $statusCode);
    }
}
