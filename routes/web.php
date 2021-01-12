<?php

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

Route::get('/', function () {
    return view('welcome');
});

/* Product Routes */
Route::get('/store', 'ProductController@index')->name('products.index');
Route::get('/store/{slug}', 'ProductController@show')->name('products.show');
Route::get('/search', 'ProductController@search')->name('products.search');

/* Cart Routes */
Route::group(['middleware' => ['auth']], function () {
    Route::get('/cart', 'CartController@index')->name('cart.index');
    Route::post('/cart/add', 'CartController@store')->name('cart.store');
    Route::patch('/cart/{rowId}', 'CartController@update')->name('cart.update');
    Route::delete('/cart/{rowId}', 'CartController@destroy')->name('cart.destroy');
    Route::post('/coupon', 'CartController@storeCoupon')->name('cart.store.coupon');
    Route::delete('/coupon', 'CartController@destroyCoupon')->name('cart.destroy.coupon');
});


/* Payment Routes */
Route::group(['middleware' => ['auth']], function () {
    Route::get('/payment', 'PaymentController@index')->name('payment.index');
    Route::post('/payment', 'PaymentController@store')->name('payment.store');
    Route::get('/thanks', 'PaymentController@thanks')->name('payment.thanks');
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
