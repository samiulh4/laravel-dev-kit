<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Web\Http\Controllers\WebController;

Route::get('/', [WebController::class, 'index']);

