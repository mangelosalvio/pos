<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


/*Route::get('/', function () {
    //return view('welcome');
    return view('cashier');
});*/

use Illuminate\Support\Facades\Auth;

Route::get('/',['middleware' => 'auth', function(){
    return view('cashier');
}]);

Route::get('/home',['middleware' => 'auth', function(){
    return view('cashier');
}]);

Route::get('/user', function(){
    return Auth::user();
});

// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');



// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');


Route::resource('tables','TableController');
Route::post('sales/void','SaleController@voidSale');
Route::get('sales/sale-inventory/{from_date}/{to_date}','ReportsController@saleInventory');
Route::get('sales/daily-sales/{from_date}/{to_date}','ReportsController@dailySales');
Route::get('sales/sales-transaction/{from_date}/{to_date}','ReportsController@salesTransaction');
Route::get('sales/voided-sales/{from_date}/{to_date}','ReportsController@voidedSales');

Route::get('report/invoice/{id}','ReportsController@invoice');

Route::get('sales/reprintInvoice','SaleController@reprintInvoice');
Route::post('sales/xread','SaleController@xread');
Route::post('sales/zread','SaleController@zread');
Route::resource('sales','SaleController');

Route::post('order','OrderController@printToKitchen');
Route::post('order/bill','OrderController@bill');
Route::post('order/cancelItem','OrderController@cancelItem');
Route::post('order/cancelOrder','OrderController@cancelOrder');

Route::get('products/code/{stock_code}', 'ProductController@stockCode');
Route::delete('products/{product_id}/{item_id}', 'ProductController@deleteItem');
Route::resource('products','ProductController');
Route::resource('category','CategoryController');

Route::resource('users','UserController');
Route::resource('roles','RoleController');
Route::resource('salesfile','SalesFileController');
/*Route::get('salesfile/{page_no}/page','SalesFileController@paginate');*/
