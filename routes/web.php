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

Route::get('/', function () {
    return view('welcome');
});


Route::post('/api/register', 'App\Http\Controllers\UserController@register' );
Route::post('/api/login', 'App\Http\Controllers\UserController@login' );
Route::post('/api/clientes', 'App\Http\Controllers\UserController@getClientes' );
Route::post('/api/operador', 'App\Http\Controllers\UserController@getOperador' );
Route::post('/api/rol', 'App\Http\Controllers\UserController@getRol' );

//Cambiar contraseña 
Route::post('/api/reset-password', 'App\Http\Controllers\ResetPasswordController@sendEmail' );
Route::post('/api/cambiar-contraseña', 'App\Http\Controllers\ChangePassword@process' );


Route::resource('/api/usuarios', 'App\Http\Controllers\UserController');
Route::resource('/api/viajes', 'App\Http\Controllers\ViajesController');
Route::resource('/api/roles', 'App\Http\Controllers\RolesController');


Route::resource('/api/imagen', 'App\Http\Controllers\RolesController');