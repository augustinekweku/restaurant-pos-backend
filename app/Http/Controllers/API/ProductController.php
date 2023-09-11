<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Product;
use App\Http\Resources\Product as ProductResource;
use App\Models\Category;
use App\Models\Item;
use App\Models\Items_inventory;
use App\Models\Table;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends BaseController
{
    public function getCategories()
    {
        $res = Category::orderBy('id', 'desc')->get();
        return $this->sendResponse($res, 'Categories retrieved successfully', 200);
    }

    public function createCategory(Request $request)
    {
        //validate request
        $this->validate($request, [
            'category_name' => 'required',
            'desc' => 'required',
            'image' => 'required',
        ]);
        $res = Category::create([
            'category_name' => $request->category_name,
            'image' => $request->image,
            'desc' => $request->desc,
        ]);
        return $this->sendResponse($res, 'Category created successfully', 200);
    }
    public function editCategory(Request $request)
    {
        //validate request
        $this->validate($request, [
            'id' => 'required', //this is the id of the category we want to edit
            'category_name' => 'required',
            'desc' => 'required',
            'image' => 'required',
        ]);
        $res = Category::where('id', $request->id)->update([
            'category_name' => $request->category_name,
            'image' => $request->image,
            'desc' => $request->desc,
        ]);
        return $this->sendResponse($res, 'Category updated successfully', 200);
    }
    public function deleteCategory($id)
    {
        //first delete the original file from the server
        // $this->deleteFileFromServer($request->image);
        //validate request 

        if (!$id) {
            return $this->sendError('Error', ['error' => 'Please provide the id of the category you want to delete']);
        }

        try {
            //code...
            $res =  Category::where('id', $id)->delete();
            return $this->sendResponse($res, 'Category deleted successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', ['error' => $th->getMessage()]);
        }
    }
    public function createItem(Request $request)
    {
        //validate request
        $this->validate($request, [
            'item_name' => 'required',
            'item_description' => 'required',
            'image' => 'required',
            'price' => 'required',
            'category_id' => 'required',
        ]);
        try {
            $res = Item::create([
                'item_name' => $request->item_name,
                'item_description' => $request->item_description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'image' => $request->image,
                'stock' => $request->stock,
                'qty_left' => $request->stock,
            ]);
            return $this->sendResponse($res, 'Item created successfully', 200);
            //code...
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', ['error' => $th->getMessage()]);
        }
    }

    public function getCategoryId($category_id)
    {
        try {
            //code...
            $data =  Category::where('id', '=', $category_id)->get('category_name');
            return $this->sendResponse($data, 'Category retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', ['error' => $th->getMessage()]);
        }
    }

    public function getItems()
    {

        try {
            //code...
            $items = Item::paginate(10);
            return  $this->sendResponse($items, 'Items retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', ['error' => $th->getMessage()]);
        }
    }

    public function editItem(Request $request)
    {
        $this->validate($request, [
            'id' => 'required', //this is the id of the item we want to edit
            'item_name' => 'required',
            'item_description' => 'required',
            'image' => 'required',
            'price' => 'required',
            'category_id' => 'required',
        ]);

        try {
            //code...
            $res =  Item::where('id', $request->id)->update([
                'item_name' => $request->item_name,
                'item_description' => $request->item_description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'image' => $request->image,
            ]);
            return $this->sendResponse($res, 'Item updated successfully', 201);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', ['error' => $th->getMessage()]);
        }
    }

    public function addStock(Request $request)
    {
        DB::beginTransaction();
        try {
            // return $request;
            $stock = $request->stock;
            $quantity_added = $request->quantity;

            $new_quantity_left = $request->old_qty_left + $quantity_added;
            $new_stock = $request->stock + $quantity_added;


            $item_stock = Item::where('id', $request->item_id)->update([
                'stock' => $new_stock,
                'qty_left' => $new_quantity_left
            ]);
            $user = Auth::user();
            Items_inventory::create([
                'old_qty' => $request->old_qty_left,
                'qty_added' => $quantity_added,
                'new_qty_left' => $new_quantity_left,
                'old_stock' => $request->stock,
                'new_stock' => $new_stock,
                'date' => date('Y/m/d'),
                'item_id' => $request->item_id,
                'user_id' => $user->id,
            ]);
            DB::commit();
            return $this->sendResponse(['new_stock' => $new_stock, 'new_qty_left' => $new_quantity_left], 'Item stock updated successfully', 201);
        } catch (Exception $e) {
            DB::rollback();
            return $e;
        }
    }

    public function deleteItem(Request $request)
    {
        //first delete the original file from the server
        // $this->deleteFileFromServer($request->image);
        //validate request
        $this->validate($request, [
            'id' => 'required'
        ]);
        try {
            //code...
            $res =  Item::where('id', $request->id)->delete();
            return $this->sendResponse($res, 'Item deleted successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', ['error' => $th->getMessage()]);
        }
    }

    public function createTable(Request $request)
    {
        //validate request
        $this->validate($request, [
            'table_name' => 'required|unique:Tables',
            'status' => 'required'
        ]);
        try {
            $res =  Table::create([
                'table_name' => $request->table_name,
                'status' => $this->tableStatuses['empty'],
            ]);
            return $this->sendResponse($res, 'Table created successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', ['error' => $th->getMessage()]);
        }
    }
    public function getEmptyAndUnpaidTables()
    {

        try {
            //code...
            //return Table::where('status', 3)->get();

            $getTables = DB::table('Tables')->where('status', '=', $this->tableStatuses['unpaid'])
                ->orWhere('status', '=', $this->tableStatuses['empty'])
                ->limit(6)->get();
            return $this->sendResponse($getTables, 'Tables retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }
    public function getAllTables()
    {
        try {
            //code...
            $res =  Table::orderBy('id', 'desc')->get();
            return $this->sendResponse($res, 'Tables retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }
    public function getItemsForPos()
    {
        try {
            //code...
            $res =  Item::where('qty_left', '>', 1)->orderBy('id', 'desc')->get();
            return $this->sendResponse($res, 'Items retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }
}
