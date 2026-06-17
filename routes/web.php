<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\SubscriberController;
use \App\Http\Controllers\SubscriptionController;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\SubscriptionTypesController;
use \App\Http\Controllers\SessionReportController;
use \App\Http\Controllers\ExpenseController;







Route::get('/subscribers/vectors', [SubscriberController::class, 'getSubscribers']);

Route::get('/test', [ExpenseController::class, 'index']);

Route::get("/", function () {
    return view("Login");
});
Route::get('/users', [UserController::class, 'index']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/user-face', [UserController::class, 'checkFace']);

// ======================================================================

Route::post('/subscriber-face', [SubscriberController::class, 'checkFace']);
Route::middleware(['user'])->group(function () {
    
    
    Route::resource('expenses', ExpenseController::class);
    Route::resource('/subscribers', SubscriberController::class);
    Route::resource('/subscriptions', SubscriptionController::class);
    Route::resource('/subscription-type', SubscriptionTypesController::class);
    

    Route::post('/session-report', [SessionReportController::class, 'show']);
   
    Route::post('/set-water-balance',[SessionReportController::class , 'setWaterBalance']);

    Route::get('/entranceGate', function () {
        return view('EntranceGate');
    });




    Route::get('/report', function () {
        return view('Report');
    });






});