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

Route::get('/scanfolder', 'FileIndexController@readInputFiles');
Route::get('/entities', 'EntityListController@index')->middleware('admin');
Route::get('/check_user/{email}', 'UserController@isAdmin');
Route::get('/index_exceptions', 'IndexProcessExceptionController@index')->middleware('admin');
Route::get('/index_exception/{id}', 'IndexProcessExceptionController@show')->middleware('admin');
Route::post('/reindex_file', 'IndexProcessExceptionController@store')->middleware('admin');
