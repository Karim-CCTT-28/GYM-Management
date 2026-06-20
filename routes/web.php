<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\SubscriberController;
use \App\Http\Controllers\SubscriptionController;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\SubscriptionTypesController;
use \App\Http\Controllers\SessionReportController;
use \App\Http\Controllers\ExpenseController;
use \App\Http\Controllers\NoteController;







Route::get('/subscribers/vectors', [SubscriberController::class, 'getSubscribers']);

Route::get('/test', [SessionReportController::class, 'updateBalance']);

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
    Route::resource('/subscription-types', SubscriptionTypesController::class);
    Route::resource('/notes', NoteController::class);
    Route::resource('/users',UserController::class)->except('index');
    Route::post('/session-report', [SessionReportController::class, 'show']);
   
    Route::post('/set-water-balance',[SessionReportController::class , 'setWaterBalance']);

    Route::get('/entranceGate', function () {
        return view('EntranceGate');
    });




    Route::get('/report', function () {
        return view('Report');
    });




    Route::get('/subscribers-today' , [SubscriberController::class , 'getCheckInToday']);


    Route::get('/management' , function(){
        return view("Management");
    });




    Route::get('/employees' , [UserController::class , 'getEmployees']);
    Route::get('/reports' , [SessionReportController::class , 'index']);
    Route::get('/admin-report/{id}' , [SessionReportController::class , 'adminShow']);

});