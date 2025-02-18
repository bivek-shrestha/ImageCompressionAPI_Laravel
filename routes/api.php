<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ImageUploadController;

Route::post('/upload', [ImageUploadController::class, 'upload']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
