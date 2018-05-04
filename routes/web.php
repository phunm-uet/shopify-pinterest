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

Route::get('/','HomeController@index')->name('home');
Route::get('/history','HistoryController@index')->name('history');
Route::get('/change-password','HomeController@changePassword')->name('change-password');
Route::post('/post-change-password','HomeController@postChangePassword')->name('post-change-password');

Auth::routes();

Route::post('/api/update-queue','HomeController@updateStatusQueue');