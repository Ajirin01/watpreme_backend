<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TemplateMessageController;
use App\Http\Controllers\CustomAttributeController;
use App\Http\Controllers\TopicsController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ContactAttributeController;
use App\Http\Controllers\MediaUploadController;
use App\Http\Controllers\GeneralSettingController;
use App\Http\Controllers\BusinessProfileController;
use App\Http\Controllers\BusinessController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthenticationController::class, 'register']);
Route::post('/login', [AuthenticationController::class, 'login']);
Route::post('/logout', [AuthenticationController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/unauthorize', function(){
    return response()->json(['message'=> 'unauthorized']);
})->name('unauthorize');


Route::post('/send-message', [App\Http\Controllers\MessangerController::class, 'sendMessage']);

Route::apiResource('template-messages', TemplateMessageController::class);
Route::get('upload-template/{id}', [TemplateMessageController::class, 'uploadTemplate']);
Route::get('upload-update-template/{id}', [TemplateMessageController::class, 'uploadUpdateTemplate']);


Route::post('upload-file',[TemplateMessageController::class, 'UploadFile']);
Route::get('sync-templates', [TemplateMessageController::class, 'syncTemplates']);


Route::apiResource('custom-attributes', CustomAttributeController::class);

Route::apiResource('broadcasts', BroadcastController::class);
Route::post('query-broadcasts', [BroadcastController::class, 'queryBroadcasts']);


Route::apiResource('contacts', ContactController::class);
Route::apiResource('contact-attributes', ContactAttributeController::class);

Route::apiResource('topics', TopicsController::class);


Route::apiResource('media', MediaUploadController::class);
Route::get('create-upload-session', [MediaUploadController::class, 'createSession']);

Route::apiResource('businesses', BusinessController::class);

Route::apiResource('general-settings', GeneralSettingController::class);
Route::post('query-general-settings', [GeneralSettingController::class, 'queryGeneralSettings']);

Route::apiResource('business-profiles', BusinessProfileController::class);
Route::post('query-business-profiles', [BusinessProfileController::class, 'queryBusinessProfiles']);

Route::apiResource('teams', TeamController::class);
Route::post('query-teams', [TeamController::class, 'queryTeams']);

Route::apiResource('operators', OperatorController::class);
Route::post('query-operators', [OperatorController::class, 'queryOperators']);





