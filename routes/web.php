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

Route::get('/group/{hash}', 'GroupController@show')->where('hash', '[0-9a-f]{64}');
Route::get('/group/register', 'GroupController@create');
Route::post('/group/register', 'GroupController@store');
Route::get('/group/poster_pdf/{hash}', 'GroupController@poster_pdf');
Route::get('/group/pop_pdf/{hash}', 'GroupController@pop_pdf');
Route::get('/group/png/{size}/{hash}', 'GroupController@png');
Route::get('recipient/register/{hash}', 'RecipientController@registerView');
Route::get('recipient/unregister/{hash}', 'RecipientController@unregisterView');
Route::post('recipient/register', 'RecipientController@register');
Route::post('recipient/unregister', 'RecipientController@unregister');

Route::get('/terms_user', function () {
    return view('terms_user');
});
Route::get('/terms_organization', function () {
    return view('terms_organization');
});

Route::middleware('auth.basic')->group(function () {
    Route::get('recipient/search', 'RecipientController@searchView');
    Route::post('recipient/search', 'RecipientController@search');
    Route::post('recipient/download', 'RecipientController@download');
    Route::get('group/select2_search', 'GroupController@select2Search');
});
