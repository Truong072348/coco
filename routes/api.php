<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login', 'api\UserController@login');
Route::post('register', 'api\UserController@register');
Route::post('user/{id}', 'api\UserController@update');
Route::get('user/{id}', 'api\UserController@getUser');
Route::get('user', 'api\UserController@index');
Route::post('user-avatar/{id}', 'api\UserController@updateAvatar');

Route::post('shop/{id}', 'api\UserController@createShop');
Route::get('shop/{user}', 'api\UserController@getShop');

Route::get('product','api\ProductController@index');
Route::get('product/{id}','api\ProductController@show');
Route::post('product','api\ProductController@store');
Route::put('product/{id}','api\ProductController@update');
Route::delete('product/{id}', 'api\ProductController@delete');
Route::get('products-by-type/{type}', 'api\ProductController@getByType');
Route::get('products-by-category/{category}', 'api\ProductController@getByCategory');
Route::get('products-by-shop/{shop}', 'api\ProductController@getByShop');

Route::get('categories', 'api\CategoryController@index');
Route::get('categories/{id}', 'api\CategoryController@show');
Route::post('categories', 'api\CategoryController@store');
Route::put('categories/{id}', 'api\CategoryController@update');
Route::delete('categories/{id}', 'api\CategoryController@delete');

Route::get('types', 'api\TypeController@index');
Route::get('types/{id}', 'api\TypeController@show');
Route::post('types', 'api\TypeController@store');
Route::put('types/{id}', 'api\TypeController@update');
Route::delete('types/{id}', 'api\TypeController@delete');
Route::get('types-by-category/{category}', 'api\TypeController@getByCategory');


Route::get('images/{product}', 'api\ImageController@getImages');
Route::post('images/{product}', 'api\ImageController@store');
Route::post('images-update/{id}', 'api\ImageController@update');
Route::delete('images/{id}', 'api\ImageController@delete');


Route::get('order/{id}', 'api\OrderController@getOrder');
Route::get('order-by-user/{user}', 'api\OrderController@getOrderByUser');
Route::get('order-detail/{order}', 'api\OrderController@getDetailOrder');
Route::post('order/{user}','api\OrderController@store');
//add product to details order create order
Route::post('add-product-order/{order}/{product}', 'api\OrderController@addProductOrder');
Route::put('update-cost-order/{order}','api\OrderController@updateCostOrder');
Route::put('update-status-order/{order}/{status}', 'api\OrderController@updateStatusOrder');

Route::get('histories/{user}','api\HistoryController@getHistory');
Route::get('histories-order/{user}', 'api\HistoryController@getOrderHistory');

Route::post('search-product-by-name', 'api\SearchController@searchProduct');
Route::post('search-product-by-name-type/{type}', 'api\SearchController@searchProductByNameType');
Route::post('search-product-by-price/{price}', 'api\SearchController@searchProductByPrice');

Route::get('cart/{user}','api\CartController@show');
Route::post('cart/{user}/{product}','api\CartController@store');
Route::post('cart-pay/{user}','api\CartController@cartPay');
Route::put('cart-delete-item/{user}/{product}', 'api\CartController@cartDeleteItem');
Route::put('cart-update-item/{user}/{product}', 'api\CartController@cartUpdateItem');

Route::post('comment/{userId}/{productId}', 'api\CommentsController@saveComment');
Route::get('province', 'api\ProvinceDistrictWardController@getProvince');
Route::get('district-by-province/{provinceId}', 'api\ProvinceDistrictWardController@getDistrictByProvince');
Route::get('ward-by-district/{districtId}', 'api\ProvinceDistrictWardController@getWardbyDistrict');
// Route::resource('payment/{userId}', 'api\PaymentController');
Route::post('payment/create', 'api\PaymentController@create')->name('payment.create');
Route::get('districts', 'api\ProvinceDistrictWardController@getDistrict');

