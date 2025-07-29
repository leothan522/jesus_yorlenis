<?php

use App\Http\Controllers\Dashboard\ExportController;
use Illuminate\Support\Facades\Route;

Route::/*middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])*/
    prefix('dashboard')
    ->group(function () {

        Route::get('export/control/{id}/prenatal', [ExportController::class, 'exportControlPrenatal'])->name('export.control');

});


