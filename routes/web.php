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
//     return view('base');
// });

Route::get('/', 'CommentsController@index')->name('Home');
Route::post('store', 'CommentsController@store');
Route::delete('destroy/{id}', 'CommentsController@destroy');
Route::post('edit/{id}', 'CommentsController@editComment');