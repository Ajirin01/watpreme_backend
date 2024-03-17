<?php

use Illuminate\Support\Facades\Route;



Route::post('/webhook', [App\Http\Controllers\WebhookController::class, 'webhookPost']);

Route::get('/webhook', [App\Http\Controllers\WebhookController::class, 'webhookGet']);


Route::get('/webhook_log', function(){
    return view('webhook_log');
});

Route::get('/messanger', [App\Http\Controllers\MessangerController::class, 'messanger']);


Route::get('/', function () {
    return view('welcome');
});
