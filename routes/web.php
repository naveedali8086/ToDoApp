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

Auth::routes();

Route::middleware('auth')->group(function () {

    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('to-do-list', 'ToDoController@showToDoList');

    Route::post('create-todo', 'ToDoController@create');

    Route::post('edit-todo/{id}', 'ToDoController@edit');

    Route::post('delete-todo/{id}', 'ToDoController@delete');

    Route::post('toggle-status/{id}', 'ToDoController@edit')->name('toggle_status');

});