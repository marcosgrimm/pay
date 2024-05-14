<?php

use App\Http\Controllers\Api\V1\PaymentsController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json(['message' => 'Pong. You are authenticated.']);
});
Route::get('payments', [PaymentsController::class, 'index'])->name('payments.index');
Route::get('payments/{id}', [PaymentsController::class, 'show'])->name('payments.show');
Route::post('payments', [PaymentsController::class, 'store'])->name('payments.store');

require __DIR__.'/auth.php';
