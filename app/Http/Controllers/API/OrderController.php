<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Order;
use App\Models\Table;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Models\Creditor_order;
use App\Models\Creditor_order_details;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends BaseController
{
    public function createOrderDetails(Request $request)
    {
        //validate request
        // $this->validate($request, [
        //     'item_id' => 'required',
        //     'item_name' => 'required',
        //     'order_id' => 'required',
        //     'price' => 'required',
        //     'quantity' => 'required',
        //     'amount' => 'required',
        // ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();


            //insert order details
            $order = Order::create([
                'table_id' => $request->table_id,
                'table_name' => $request->table_name,
                'order_total' => $request->order_total,
                'order_number' => $request->order_number,
                'invoice_number' => $request->invoice_number,
                'order_type' => $request->order_type,
                'status' => $this->orderStatuses['hold'],
                'user_id' => $user->id
            ]);
            $order->save();
            $order_details = $request->order_details;
            $orderDetails = [];
            foreach ($order_details as $od => $x) {
                array_push($orderDetails, [
                    'item_id' => $x['item_id'], 'item_name' => $x['item_name'],
                    'order_id' => $order->id, 'price' => $x['price'], 'quantity' => $x['quantity'], 'amount' => $x['amount']
                ]);
                //echo json_encode($x['item_name']);

                //update the quantity left
                $getItemQtyLeft = DB::table('Items')->where('id', '=', $x['item_id'])->get('qty_left');
                $old_qty = $getItemQtyLeft[0]->qty_left;
                $new_qty_left = $old_qty - $x['quantity'];
                Item::where('id', $x['item_id'])->update([
                    'qty_left' => $new_qty_left
                ]);
            }
            OrderDetail::insert($orderDetails);
            Table::where('id', $request->table_id)->update([
                'status' => 1,
            ]);
            //$updateTable->save();


            DB::commit();
            return $this->sendResponse($order, 'Order created successfully', 201);
        } catch (Exception $e) {
            DB::rollback();
            return $this->sendError('Error', $e->getMessage(), 500);
        }
    }

    public function createCreditorOrder(Request $request)
    {

        //validate request
        $this->validate($request, [
            "company_id" => "required",
            "order_total" => "required",
            "order_number" => "required",
            "invoice_number" => "required",
            "status" => "required",
            "user_id" => "required",
            "notes" => "required",
            "due_date" => "required",
            "order_details" => "required",
        ]);


        DB::beginTransaction();
        try {
            $user = Auth::user();

            //insert order details
            $order = Creditor_order::create([
                'company_id' => $request->company_id,
                'order_total' => $request->order_total,
                'order_number' => $request->order_number,
                'invoice_number' => $request->invoice_number,
                'status' => $this->orderStatuses['hold'],
                'user_id' => $user->id,
                'notes' => $request->notes,
                'due_date' => $request->due_date
            ]);
            $order->save();
            $order_details = $request->order_details;
            $orderDetails = [];
            foreach ($order_details as $od => $x) {
                array_push($orderDetails, [
                    'item_id' => $x['item_id'], 'item_name' => $x['item_name'],
                    'creditor_order_id' => $order->id, 'price' => $x['price'], 'quantity' => $x['quantity'], 'amount' => $x['amount']
                ]);
                //echo json_encode($x['item_name']);

            }
            Creditor_order_details::insert($orderDetails);
            DB::commit();
            return $this->sendResponse($order, 'Order created successfully', 201);
            //code...
        } catch (Exception $e) {
            DB::rollback();
            return $this->sendError('Error', $e->getMessage(), 500);
        }
    }

    public function getRequestedOrders(Request $request)
    {

        try {
            //code...
            $orders = Order::where('status', '=', $this->orderStatuses['hold'])->orWhere('ready', 0)->with('orderDetails')->paginate(10);
            return $this->sendResponse($orders, 'Orders retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Orders not retrieved', $th->getMessage(), 500);
        }


        //$getOrders = DB::table('Orders')->where('status', '=', 2)-with('orderDetails')->get();
        //return $getOrders;
    }
    public function getLatestRequestedOrder(Request $request)
    {

        try {
            $order = Order::where('status', '=', $this->orderStatuses['hold'])->orWhere('ready', 0)->with('orderDetails')->orderBy('id', 'desc')->first();
            return $this->sendResponse($order, 'Order retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Order not retrieved', $th->getMessage(), 500);
        }
    }


    public function getRequestedCreditorOrders(Request $request)
    {
        try {
            //code...
            $orders = Creditor_order::where('status', '=', $this->orderStatuses['hold'])->with('creditor_order_details', 'company')->paginate(10);
            return $this->sendResponse($orders, 'Orders retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Orders not retrieved', $th->getMessage(), 500);
        }
    }

    public function creditorOrderConfirmedByCook($order_id)
    {
        try {
            //code...
            $order = Creditor_order::where('id', $order_id)->update([
                'ready' => 1,
                'status' => $this->orderStatuses['unpaid']
            ]);
            return $this->sendResponse($order, 'Order updated successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Order not updated', $th->getMessage(), 500);
        }
    }

    public function getReadyOrders(Request $request)
    {

        try {
            //code...
            $orders = Order::where(['status' => $this->orderStatuses['unpaid'], 'take_away_cleared' => 0])->with('orderDetails')->paginate(10);
            return $this->sendResponse($orders, 'Orders retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Orders not retrieved', $th->getMessage(), 500);
        }

        //$getOrders = DB::table('Orders')->where('status', '=', 2)-with('orderDetails')->get();
        //return $getOrders;
    }

    public function getCreditorReadyOrders(Request $request)
    {
        try {
            //code...
            $orders = Creditor_order::where('status', $this->orderStatuses['unpaid'])->with('creditor_order_details', 'company')->paginate(10);
            return $this->sendResponse($orders, 'Orders retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Orders not retrieved', $th->getMessage(), 500);
        }

        //$getOrders = DB::table('Orders')->where('status', '=', 2)-with('orderDetails')->get();
        //return $getOrders;
    }


    public function orderConfirmedByCook($order_id, $order_type)
    {

        if ($order_type == $this->orderTypes['takeaway']) {
            try {
                //code...
                $res = Order::where('id', $order_id)->update([
                    'ready' => 1,
                    'status' => $this->orderStatuses['unpaid'],
                ]);
                return $this->sendResponse($res, 'Order updated successfully', 200);
            } catch (\Throwable $th) {
                //throw $th;
                return $this->sendError('Order not updated', $th->getMessage(), 500);
            }
        }
        if ($order_type == $this->orderTypes['table']) {
            try {
                //code...
                $res = Order::where('id', $order_id)->update([
                    'ready' => 1,
                    'status' => $this->orderStatuses['unpaid']
                ]);
                return $this->sendResponse($res, 'Order updated successfully', 200);
            } catch (\Throwable $th) {
                //throw $th;
                return $this->sendError('Order not updated', $th->getMessage(), 500);
            }
        }
    }

    public function orderAbortedByCook($order_id, $order_type)
    {
        if ($order_type == $this->orderTypes['takeaway']) {
            try {
                //code...
                $res = Order::where('id', $order_id)->update([
                    'ready' => 0,
                    'status' => $this->orderStatuses['aborted'],
                ]);
                return $this->sendResponse($res, 'Order updated successfully', 200);
            } catch (\Throwable $th) {
                //throw $th;
                return $this->sendError('Order not updated', $th->getMessage(), 500);
            }
        }
        if ($order_type == $this->orderTypes['table']) {
            try {
                //code...
                $res = Order::where('id', $order_id)->update([
                    'ready' => 0,
                    'status' => $this->orderStatuses['aborted'],
                ]);
                return $this->sendResponse($res, 'Order updated successfully', 200);
            } catch (\Throwable $th) {
                //throw $th;
                return $this->sendError('Order not updated', $th->getMessage(), 500);
            }
        }
    }

    public function clearTakeAwayOrder($order_id)
    {
        try {
            //code...
            $res = Order::where('id', $order_id)->update([
                'ready' => 1,
                'take_away_cleared' => 1,
                'status' => $this->orderStatuses['cleared'],
            ]);
            return $this->sendResponse($res, 'Order updated successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Order not updated', $th->getMessage(), 500);
        }
    }

    public function checkoutOrder(Request $request)
    {
        DB::beginTransaction();
        try {
            $order = Order::where('id', $request->id)->update([
                'status' => $this->orderStatuses['cleared'],
                'balance' => $request->balance,
                'paid' => $request->paid,
            ]);
            Table::where('id', $request->table_id)->update([
                'status' => 3
            ]);
            DB::commit();
            return $this->sendResponse($order, 'Order updated successfully', 200);
        } catch (Exception $e) {
            DB::rollback();
            return $this->sendError('Error', $e->getMessage(), 500);
        }
    }

    public function checkoutTakeAwayOrder(Request $request)
    {
        $user = Auth::user();

        $this->validate($request, [
            'order_details' => 'required',
            'total' => 'required',
            'paid' => 'required',
            'balance' => 'required',
            'order_number' => 'required',
            'invoice_number' => 'required|unique:Orders',
            'order_type' => 'required',
        ]);

        //insert order details
        DB::beginTransaction();
        try {
            $order = Order::create([

                'order_total' => $request->total,
                'paid' => $request->paid,
                'balance' => $request->balance,
                'order_number' => $request->order_number,
                'invoice_number' => $request->invoice_number,
                'order_type' => $request->order_type,
                'order_type' => $request->order_type,
                'order_number' => $request->order_number,
                'status' => 1,
                'user_id' => $user->id,
            ]);
            $order->save();
            $order_details = $request->order_details;
            $orderDetails = [];
            foreach ($order_details as $od => $x) {
                array_push($orderDetails, [
                    'item_id' => $x['item_id'], 'item_name' => $x['item_name'],
                    'order_id' => $order->id, 'price' => $x['price'], 'quantity' => $x['quantity'], 'amount' => $x['amount']
                ]);
                //echo json_encode($x['item_name']);

                //update the quantity left
                $getItemQtyLeft = DB::table('Items')->where('id', '=', $x['item_id'])->get('qty_left');
                $old_qty = $getItemQtyLeft[0]->qty_left;
                $new_qty_left = $old_qty - $x['quantity'];
                Item::where('id', $x['item_id'])->update([
                    'qty_left' => $new_qty_left
                ]);
            }
            OrderDetail::insert($orderDetails);
            //$updateTable->save();
            DB::commit();
            return $this->sendResponse($order, 'Order updated successfully', 201);
        } catch (Exception $e) {
            DB::rollback();
            return $this->sendError('Error', $e->getMessage(), 500);
        }
    }

    public function checkoutCreditorOrder(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'balance' => 'required',
            'paid' => 'required',
            'payment_type' => 'required',
        ]);
        try {
            //code...
            $res =  Creditor_order::where('id', $request->id)->update([
                'status' => $this->orderStatuses['cleared'],
                'balance' => $request->balance,
                'paid' => $request->paid,
                'payment_type' => $request->payment_type,
            ]);
            return $this->sendResponse($res, 'Order updated successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Order not updated', $th->getMessage(), 500);
        }
    }
}