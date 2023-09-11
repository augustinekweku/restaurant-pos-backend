<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\NotificationsController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ReportsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    //Auth
    Route::post('logout', [AuthController::class, 'logout']);


    //Admin
    Route::get('roles', [AdminController::class, 'getRoles']);
    Route::post('create-role', [AdminController::class, 'createRole']);

    Route::post('create-user', [AdminController::class, 'createUser']);
    Route::post('edit-user', [AdminController::class, 'editUser']);
    Route::delete('delete-user', [AdminController::class, 'deleteUser']);
    Route::get('users', [AdminController::class, 'getUsers']);


    Route::resource('products', ProductController::class);
    //Products
    Route::get('categories', [ProductController::class, 'getCategories']);
    Route::post('create-category', [ProductController::class, 'createCategory']);
    Route::post('edit-category', [ProductController::class, 'editCategory']);
    Route::delete('delete-category/{id}', [ProductController::class, 'deleteCategory']);
    Route::get('get-category/{id}', [ProductController::class, 'getCategoryId']);


    //Items
    Route::get('products', [ProductController::class, 'getItems']);
    Route::post('create-product', [ProductController::class, 'createItem']);
    Route::post('edit-product', [ProductController::class, 'editItem']);
    Route::delete('delete-product', [ProductController::class, 'deleteItem']);
    Route::post('add-stock', [ProductController::class, 'addStock']);
    Route::get('get-items-for-pos', [ProductController::class, 'getItemsForPos']);

    Route::post('create-table', [ProductController::class, 'createTable']);
    Route::get('get-empty-tables', [ProductController::class, 'getEmptyAndUnpaidTables']);
    Route::get('get-tables', [ProductController::class, 'getAllTables']);

    //Orders

    Route::post('create-order', [OrderController::class, 'createOrderDetails']);
    Route::post('create-creditor-order', [OrderController::class, 'createCreditorOrder']);
    Route::get('requested-orders', [OrderController::class, 'getRequestedOrders']);
    Route::get('latest-order', [OrderController::class, 'getLatestRequestedOrder']);
    Route::post('/confirm-order/{order_id}/{order_type}', [OrderController::class, 'orderConfirmedByCook']);
    Route::get('get-ready-orders', [OrderController::class, 'getReadyOrders']);
    Route::get('creditor-orders', [OrderController::class, 'getRequestedCreditorOrders']);
    Route::post('confirm-creditor-order/{id}', [OrderController::class, 'creditorOrderConfirmedByCook']);
    Route::get('get-ready-creditor-orders', [OrderController::class, 'getCreditorReadyOrders']);
    Route::post('abort-order/{order_id}/{order_type}', [OrderController::class, 'orderAbortedByCook']);
    Route::post('clear-takeaway-order/{order_id}', [OrderController::class, 'clearTakeAwayOrder']);
    Route::post('checkout-order', [OrderController::class, 'checkoutOrder']);
    Route::post('checkout-takeaway-order', [OrderController::class, 'checkoutTakeAwayOrder']);
    Route::post('checkout-creditor-order', [OrderController::class, 'checkoutCreditorOrder']);



    //Company
    Route::get('companies', [CompanyController::class, 'getCompanies']);
    Route::post('create-company', [CompanyController::class, 'createCompany']);
    Route::post('edit-company', [CompanyController::class, 'editCompany']);
    Route::delete('delete-company', [CompanyController::class, 'deleteCompany']);

    //Reports
    Route::get('get-cleared-order-items', [ReportsController::class, 'getClearedOrderItems']);
    Route::get('get-sales-for-item/{fromDate}/{toDate}/{item_id}', [ReportsController::class, 'getDateRangeForItem']);
    Route::get('get-sales-for-creditor-orders/{fromDate}/{toDate}/{item_id}', [ReportsController::class, 'getDateRangeForCreditorItem']);
    Route::get('get-items-for-report', [ReportsController::class, 'getItemsForReport']);
    Route::get('get-items-for-creditor-report', [ReportsController::class, 'getItemsForCreditorReport']);
    Route::get('get-all-items', [ReportsController::class, 'getAllItems']);
    Route::get('/fetch-item/{item_id}', [ReportsController::class, 'fetchItem']);
    Route::get('/fetch-creditor-item/{item_id}', [ReportsController::class, 'fetchCreditorItem']);

    Route::get('/get-cleared-creditor-orders', [ReportsController::class, 'getClearedCreditorOrders']);
    Route::get('/get-cleared-orders', [ReportsController::class, 'getClearedOrders']);

    Route::get('/get-inventory-records', [ReportsController::class, 'getInventoryRecords']);

    //Notifications
    Route::get('/get-ready-orders-count', [NotificationsController::class, 'getReadyOrdersCount']);
    Route::get('/get-requested-orders-count/{old_count}', [NotificationsController::class, 'getRequestedOrdersCount']);
    Route::get('/get-ready-credit-orders-count', [NotificationsController::class, 'getReadyCreditOrdersCount']);
    Route::get('/get-requested-credit-orders-count/{old_count}', [NotificationsController::class, 'getRequestedCreditOrdersCount']);
});
