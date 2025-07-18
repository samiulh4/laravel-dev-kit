<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Authentication\Http\Controllers\AuthenticationAdminController;

Route::get('/unauthenticated', [AuthenticationAdminController::class, 'unAuthenticated']);

Route::group(['prefix' => '/admin'], function () {
    Route::get('/sign-up', [AuthenticationAdminController::class, 'adminSignUp']);
    Route::get('/sign-in', [AuthenticationAdminController::class, 'adminSignIn']);
    Route::post('/sign-up/store', [AuthenticationAdminController::class, 'adminSignUpStore']);
    Route::post('/sign-in/submit', [AuthenticationAdminController::class, 'adminSignInSubmit']);
});

Route::post('/sign-out', [AuthenticationAdminController::class, 'signOut'])->middleware('auth');