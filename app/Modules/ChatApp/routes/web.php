<?php

use Illuminate\Support\Facades\Route;
use App\Modules\ChatApp\Http\Controllers\ChatAppController;

Route::get('/web/chat/public-message', [ChatAppController::class, 'webPublicMessage']);
Route::post('/web/chat/public-message/send', [ChatAppController::class, 'webPublicMesssageSend']);
