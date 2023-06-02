<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoardsController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\UserController;
use App\Mail\OrderShipped;
use Illuminate\Support\Facades\Mail;
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

Route::get('/send',function(){
    Mail::to('awfawf123@naver.com')->send(new OrderShipped());
});

// Boards
Route::resource('/boards', BoardsController::class);

// Users
Route::prefix('/users')->group( function() {
    Route::get('/login', [UserController::class, 'login'])->name('users.login');
    Route::post('/loginpost', [UserController::class, 'loginpost'])->name('users.login.post');
    Route::get('/registration', [UserController::class, 'registration'])->name('users.registration');
    Route::post('/registrationpost', [UserController::class, 'registrationpost'])->name('users.registration.post');
    Route::get('/logout',[UserController::class, 'logout'])->name('users.logout');
    Route::get('/withdraw',[UserController::class, 'withdraw'])->name('users.withdraw');
    Route::get('/update', [UserController::class, 'update'])->name('users.update');
    Route::post('/updatepost', [UserController::class, 'updatepost'])->name('users.update.post');
});

Route::get('/send',[MailController::class,'index']);