<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Pusher\Pusher;

use App\Jobs\ProcessQueueJob;


// Route::post('/webhook', [App\Http\Controllers\WebhookController::class, 'webhookPost']);

$token = env('WHATSAPP_TOKEN');


Route::post('/webhook', function (Request $request) use ($token) {
    // Parse the request body from the POST
    $body = $request->all();

    // Trigger Pusher event
    $pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'useTLS' => true
    ]);
    $pusher->trigger('whatsapp-events', 'message-received', ['message' => $body['entry'][0]['changes'][0]['value']]);
    
});

Route::get('/webhook', [App\Http\Controllers\WebhookController::class, 'webhookGet']);


Route::get('/webhook_log', function(){
    return view('webhook_log');
});

Route::get('/messanger', [App\Http\Controllers\MessangerController::class, 'messanger']);

Route::get('/facebook', function(){
    return view('facebook');
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/add-to-queue', function(){
    ProcessQueueJob::dispatch();
    return "Job added to the queue!";
});

Route::get('/command', function(){
    return view('command');
});

Route::post('/run-command', [App\Http\Controllers\CommandController::class, 'run'])->name('run.command');


