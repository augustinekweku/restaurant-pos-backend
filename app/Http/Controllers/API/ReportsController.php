<?php

namespace App\Http\Controllers\API;

use DateTime;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Models\Creditor_order;
use App\Models\Items_inventory;
use App\Models\Creditor_order_details;

class ReportsController extends BaseController
{
    public function getClearedOrderItems()
    {

        try {
            $data = OrderDetail::whereHas('order', function ($query) {
                $query->where('status', 'cleared');
            })->with('item', 'order')->get();
            $total_quantity = OrderDetail::whereHas('order', function ($query) {
                $query->where('status', 'cleared');
            })->with('item', 'order')->sum('quantity');
            $total_amount = OrderDetail::whereHas('order', function ($query) {
                $query->where('status', 'cleared');
            })->with('item', 'order')->sum('amount');

            return $this->sendResponse([
                'total_amount' => $total_amount,
                'data' => $data,
                'total_quantity' => $total_quantity,
            ], 'Items retrieved successfully', 200);
            //code...
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }
    //function for adding one day
    public function addOneDay($date)
    {
        return date('Y-m-d', strtotime("+1 day", strtotime($date)));
    }
    //get the date range for the salesof  a particular item
    public function getDateRangeForItem($fromDate, $toDate, $item_id)
    {
        try {
            //code...
            //return $fromDate;
            $newEndDate = date('Y-m-d', strtotime("+1 day", strtotime($toDate)));

            $getDateData = OrderDetail::whereHas('order', function ($query) use ($fromDate, $newEndDate) {
                $query->whereBetween('created_at', [$fromDate, $newEndDate])->where('status', 'cleared');
            })->with('item', 'order')->where('item_id', '=', $item_id)->get();

            $total_amount = OrderDetail::whereHas('order', function ($query) use ($fromDate, $newEndDate) {
                $query->whereBetween('created_at', [$fromDate, $newEndDate])->where('status', 'cleared');
            })->with('item', 'order')->where('item_id', '=', $item_id)->sum('amount');

            $total_quantity = OrderDetail::whereHas('order', function ($query) use ($fromDate, $newEndDate) {
                $query->whereBetween('created_at', [$fromDate, $newEndDate])->where('status', 'cleared');
            })->with('item', 'order')->where('item_id', '=', $item_id)->sum('quantity');

            $this->sendResponse([
                'total_amount' => $total_amount,
                'getDateData' => $getDateData,
                'total_quantity' => $total_quantity,
            ], 'Items retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }
    //get the date range for the salesof  a particular creditor item
    public function getDateRangeForCreditorItem($fromDate, $toDate, $item_id)
    {


        try {
            //code...
            //return $fromDate;
            //making a query based on the relationship 'creditor_order'
            $getDateData = Creditor_order_details::whereHas('creditor_order', function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('created_at', [$fromDate, $this->addOneDay($toDate)])->where('status', 1);
            })->with('item', 'creditor_order')->where('item_id', '=', $item_id)->get();

            $total_amount = Creditor_order_details::whereHas('creditor_order', function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('created_at', [$fromDate, $this->addOneDay($toDate)])->where('status', 1);
            })->with('item', 'creditor_order')->where('item_id', '=', $item_id)->sum('amount');

            $total_quantity = Creditor_order_details::whereHas('creditor_order', function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('created_at', [$fromDate, $this->addOneDay($toDate)])->where('status', 1);
            })->with('item', 'creditor_order')->where('item_id', '=', $item_id)->sum('quantity');

            return $this->sendResponse([
                'total_amount' => $total_amount,
                'getDateData' => $getDateData,
                'total_quantity' => $total_quantity,
            ], 'Items retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;\
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }

    //get items for report
    public function getItemsForReport()
    {

        try {
            //code...
            $data = OrderDetail::whereHas('order', function ($query) {
                $query->where('status', 'cleared');
            })->with('item', 'order')->get();
            $total_quantity = OrderDetail::whereHas('order', function ($query) {
                $query->where('status', 'cleared');
            })->with('item', 'order')->sum('quantity');
            $total_amount = OrderDetail::with('item', 'order')->sum('amount');

            return $this->sendResponse([
                'total_amount' => $total_amount,
                'data' => $data,
                'total_quantity' => $total_quantity,
            ], 'Items retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }
    //get items for credit report
    public function getItemsForCreditorReport()
    {

        try {
            //code...
            $data = Creditor_order_details::whereHas('creditor_order', function ($query) {
                $query->where('status', 'cleared');
            })->with('item', 'creditor_order')->get();

            $total_amount = Creditor_order_details::whereHas('creditor_order', function ($query) {
                $query->where('status', 'cleared');
            })->with('item', 'creditor_order')->sum('amount');

            $total_quantity = Creditor_order_details::whereHas('creditor_order', function ($query) {
                $query->where('status', 'cleared');
            })->with('item', 'creditor_order')->sum('quantity');

            return $this->sendResponse([
                'total_amount' => $total_amount,
                'data' => $data,
                'total_quantity' => $total_quantity,
            ], 'Items retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }

    public function getAllItems()
    {
        try {
            //code...
            $res =  Item::orderBy('id', 'desc')->get();
            return $this->sendResponse($res, 'Items retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Items not retrieved', $th->getMessage(), 500);
        }
    }
    public function fetchItem($item_id)
    {


        try {
            //code...
            //return $fromDate;
            $data = OrderDetail::whereHas('order', function ($query) {
                $query->where('status', 'cleared');
            })->with('item', 'order')->where('item_id', '=', $item_id)->get();

            $total_amount = OrderDetail::whereHas('order', function ($query) {
                $query->where('status', 'cleared');
            })->with('item', 'order')->where('item_id', '=', $item_id)->sum('amount');

            $total_quantity = OrderDetail::whereHas('order', function ($query) {
                $query->where('status', 'cleared');
            })->with('item', 'order')->where('item_id', '=', $item_id)->sum('quantity');

            $this->sendResponse([
                'total_amount' => $total_amount,
                'data' => $data,
                'total_quantity' => $total_quantity,
            ], 'Items retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Items not retrieved', $th->getMessage(), 500);
        }
    }

    //get all date ranges for the salesof  a particular credit item

    public function fetchCreditorItem($item_id)
    {

        try {
            //code...
            //return $fromDate;
            $data = Creditor_order_details::whereHas('creditor_order', function ($query) {
                $query->where('status', 'cleared');
            })->with('item', 'creditor_order')->where('item_id', '=', $item_id)->get();

            $total_amount = Creditor_order_details::whereHas('creditor_order', function ($query) {
                $query->where('status', 'cleared');
            })->with('item', 'creditor_order')->where('item_id', '=', $item_id)->sum('amount');

            $total_quantity = Creditor_order_details::whereHas('creditor_order', function ($query) {
                $query->where('status', 'cleared');
            })->with('item', 'creditor_order')->where('item_id', '=', $item_id)->sum('quantity');
            $this->sendResponse([
                'total_amount' => $total_amount,
                'data' => $data,
                'total_quantity' => $total_quantity,
            ], 'Items retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Items not retrieved', $th->getMessage(), 500);
        }
    }
    public function  getClearedOrders()
    {

        try {
            //code...
            $order = Order::where('status', '=', 1)->with('user', 'orderDetails')->orderBy('id', 'desc')->get();
            $total = Order::where('status', '=', 1)->sum('order_total');

            return $this->sendResponse([
                'total' => $total,
                'order' => $order,
            ], 'Orders retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Orders not retrieved', $th->getMessage(), 500);
        }
    }

    public function getClearedCreditorOrders()
    {
        try {
            //code...
            $order = Creditor_order::where('status', '=', 1)->with('user', 'creditor_order_details', 'company')->orderBy('id', 'desc')->get();
            $total = Creditor_order::where('status', '=', 1)->sum('order_total');

            return $this->sendResponse([
                'total' => $total,
                'reports' => $order,
            ], 'Orders retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Orders not retrieved', $th->getMessage(), 500);
        }
    }


    public function getDateRange($fromDate, $toDate)
    {

        try {
            //code...
            $getDateData = Order::whereBetween('created_at', [$fromDate, $this->addOneDay($toDate)])->where('status', '=', 1)->with('user', 'orderDetails')->get();
            $total = Order::whereBetween('created_at', [$fromDate, $this->addOneDay($toDate)])->where('status', '=', 1)->sum('order_total');
            return $this->sendResponse([
                'total' => $total,
                'getDateData' => $getDateData,
            ], 'Orders retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function getClearedCreditorDateRange(Request $request, $fromDate, $toDate)
    {
        $date = new DateTime($fromDate);
        //return $fromDate;

        try {
            //code...
            $getDateData = Creditor_order::whereBetween('created_at', [$fromDate, $this->addOneDay($toDate)])->where('status', '=', 1)->with('user', 'creditor_order_details', 'company')->get();
            $total = Creditor_order::whereBetween('created_at', [$fromDate, $this->addOneDay($toDate)])->where('status', '=', 1)->sum('order_total');
            return $this->sendResponse([
                'total' => $total,
                'getDateData' => $getDateData,
            ], 'Orders retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    public function getInventoryRecords()
    {

        try {
            $res =  Items_inventory::with('user', 'item')->orderBy('id', 'desc')->get();
            return $this->sendResponse($res, 'Inventory records retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Inventory records not retrieved', $th->getMessage(), 500);
        }
    }
}
