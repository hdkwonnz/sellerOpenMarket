<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
// Auth::routes();
// Route::get('/home', 'HomeController@index')->name('home');

Auth::routes(['verify' => true]); //for email verification==>['verify' => true]

Route::get('showVerificationMsg','HomeController@showVerificationMsg')->middleware('verified');

////////////////////////////////////////////////////////////////////////////////////////////////
// seller/product
Route::get('/seller/product/test/{id}', 'Seller\ProductController@test')->middleware('auth','can:isSeller');
Route::get('/seller/product/showProductInputForm', 'Seller\ProductController@showProductInputForm')->name('seller.product.showProductInputForm')->middleware('auth','can:isSeller');
Route::get('/seller/product/getCategoryAs', 'Seller\ProductController@getCategoryAs')->name('seller.product.getCategoryAs')->middleware('auth','can:isSeller');
Route::get('/seller/product/getCategoryBbyId', 'Seller\ProductController@getCategoryBbyId')->name('seller.product.getCategoryBbyId')->middleware('auth','can:isSeller');
Route::get('/seller/product/getCategoryCbyId', 'Seller\ProductController@getCategoryCbyId')->name('seller.product.getCategoryCbyId')->middleware('auth','can:isSeller');
Route::post('/seller/product/addProduct', 'Seller\ProductController@addProduct')->name('seller.product.addProduct')->middleware('auth','can:isSeller');
Route::get('/seller/product/showMyProducts', 'Seller\ProductController@showMyProducts')->name('seller.product.showMyProducts')->middleware('auth','can:isSeller');
Route::get('/seller/product/getMyProducts', 'Seller\ProductController@getMyProducts')->name('seller.product.getMyProducts')->middleware('auth','can:isSeller');
Route::get('/seller/product/showEditProduct/{id}', 'Seller\ProductController@showEditProduct')->name('seller.product.showEditProduct')->middleware('auth','can:isSeller');
Route::get('/seller/product/getMyProductById', 'Seller\ProductController@getMyProductById')->name('seller.product.getMyProductById')->middleware('auth','can:isSeller');
Route::post('/seller/product/uploadImages', 'Seller\ProductController@uploadImages')->name('seller.product.uploadImages')->middleware('auth','can:isSeller');
// seller/seller
Route::post('/seller/reSendMailForOrder', 'Seller\SellerController@resendMailForOrder')->name('seller.seller.resendMailForOrder')->middleware('auth','can:isSeller');
Route::get('/', 'Seller\SellerController@index');
Route::get('/home', 'Seller\SellerController@index');
Route::get('/seller', 'Seller\SellerController@index')->name('seller.seller.index')->middleware('auth','can:isSeller');
Route::post('/seller/customerOrders', 'Seller\SellerController@customerOrders')->name('seller.seller.customerOrders')->middleware('auth','can:isSeller');
Route::get('/seller/customerOrdersByTerm', 'Seller\SellerController@customerOrdersByTerm')->name('seller.seller.customerOrdersByTerm')->middleware('auth','can:isSeller');
Route::get('/seller/showCustomerOrders', 'Seller\SellerController@showCustomerOrders')->name('seller.seller.showCustomerOrders')->middleware('auth','can:isSeller');
Route::post('/seller/editOrder', 'Seller\SellerController@editOrder')->name('seller.seller.editOrder')->middleware('auth','can:isSeller');
