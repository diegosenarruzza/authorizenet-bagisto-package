<?php
use Illuminate\Support\Facades\Route;
use Webkul\AuthorizeNet\Http\Controllers\AuthorizeNetController;

Route::group(['middleware' => ['web']], function () {
    Route::prefix('authorize-net')->group(function () {
        Route::post('process-order', [AuthorizeNetController::class, 'processOrder'])->name('authorizenet.process.order');
    });
});
