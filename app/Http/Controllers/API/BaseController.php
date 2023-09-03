<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{

    public $orderStatuses = [
        'cleared' => 1,
        'hold' => 2,
        'unpaid' => 3,
        'aborted' => 4,
    ];

    public $tableStatuses = [
        'occupied' => 1,
        'unpaid' => 2,
        'empty' => 3,
    ];
    public $userTypes = [
        'admin' => 'admin',
        'cashier' => 'cashier',
        'waiter' => 'waiter',
    ];
    public $orderTypes = [
        'table' => 'table',
        'takeaway' => 'takeaway',
    ];
    public function sendResponse($result, $message, $code = 200)
    {
        return response()->json([
            'message' => $message,
            'code' => $code,
            'data' => $result,
        ]);
    }

    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
