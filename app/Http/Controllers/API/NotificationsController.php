<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Creditor_order;
use App\Models\Order;
use Illuminate\Http\Request;

class NotificationsController extends BaseController
{
    public function getRequestedOrdersCount($old_count)
    {

        try {
            //code...
            $ordersCount = Order::where('status', '=', 2)->orWhere('ready', 0)->count();
            if ($old_count !== $ordersCount) {

                return $this->sendResponse($ordersCount, 'Count retrieved successfully', 200);
            }
            exit();
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }

    public function getReadyCreditOrdersCount()
    {
        try {
            //code...
            $ordersCount = Creditor_order::where(['status' => $this->orderStatuses["unpaid"]])->count();
            return $this->sendResponse($ordersCount, 'Count retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }
    public function getRequestedCreditOrdersCount($old_count)
    {

        try {
            //code...
            $ordersCount = Creditor_order::where('status', '=', $this->orderStatuses["hold"])->orWhere('ready', 0)->count();
            if ($old_count !== $ordersCount) {
                return $this->sendResponse($ordersCount, 'Count retrieved successfully', 200);
            }
            exit();
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }

    public function getReadyOrdersCount()
    {
        try {
            //code...
            $ordersCount = Order::where(['status' => $this->orderStatuses["unpaid"], 'take_away_cleared' => 0])->count();
            return $this->sendResponse($ordersCount, 'Count retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }
}
