<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


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

Route::get('/',[App\Http\Controllers\WelcomeController::class,'index'])->name('welcome')->middleware('verified'); // index

Auth::routes(['verify'=>true]);

// Chat
// Route::get('/chat',[App\Http\Controllers\ChatController::class, 'index'])->name('chat')->middleware('verified');
// Chat

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('verified');


// Route::get('/pruebas', [App\Http\Controllers\PruebaController::class, 'index']);
// Route::post('/pruebas', [App\Http\Controllers\PruebaController::class, 'index'])->name('store');

// tabla-data-tabla
// Route::view('/data-table', 'productos/data-table')->name('productos.data-table');
// Botones de la tabla para editar
// Route::view('/producto','productos.create')->name('productos.create');
// Route::view('/producto/{id}','productos.edit')->name('producto.edit');
// tabla-data-tabla

