<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/demo-random-token', [\App\Http\Controllers\Auth\AuthController::class, 'demoRandomToken']);
require __DIR__.'/auth.php';
