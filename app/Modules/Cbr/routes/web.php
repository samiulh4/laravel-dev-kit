<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Cbr\Http\Controllers\Sbs3Controller;

Route::group(['prefix' => '/admin/cbr/sbs-3', 'middleware'=> ['auth']], function () {
    Route::get('/', [Sbs3Controller::class, 'index']);


    Route::get('/bill/data/list', [Sbs3Controller::class, 'billDataList']);
    Route::post('/bill/data/get', [Sbs3Controller::class, 'getBillData']);

    Route::get('/file/upload', [Sbs3Controller::class, 'fileUpload']);
    Route::post('/file/chunk/upload-chunk', [Sbs3Controller::class, 'uploadChunk']);
    Route::post('/file/store/finalize-upload', [Sbs3Controller::class, 'finalizeUpload']);
});
