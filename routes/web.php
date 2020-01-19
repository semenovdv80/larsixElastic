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

Route::get('/createIndex', 'SearchController@createIndex')->name('search.createIndex');
Route::get('/index', 'SearchController@indexing')->name('search.indexing');
Route::get('/get/{id}', 'SearchController@get')->name('search.get');
Route::get('/search', 'SearchController@search')->name('search.search');

