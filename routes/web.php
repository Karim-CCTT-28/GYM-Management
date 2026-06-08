<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\SubscriberController;
use \App\Http\Controllers\SubscriptionController;
use \App\Http\Controllers\UserController;
Route::get("/",function(){
    return view("CheckFace");
});
Route::get("/login",function(){
    return view("Login");
});





Route::get('/entranceGate', function () {
    return view('EntranceGate');
});




Route::get('/report', function () {
    return view('Report');
});
Route::get('/expenses', function () {
    return view('Expenses');
});



// ======================================================================
Route::resource('/subscribers',SubscriberController::class);
Route::resource('/subscriptions',SubscriptionController::class);



Route::get('/users', [UserController::class, 'index']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/check-face', [UserController::class, 'checkFace']);