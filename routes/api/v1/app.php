<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\LocaleController;
use App\Http\Controllers\Api\V1\TranslationController;

Route::prefix('v1')->middleware(['auth:sanctum', 'throttle'])->group(static function (): void {
    /** Locales **/
    Route::apiResource('locales', LocaleController::class);

    /** Translations **/
    Route::apiResource('translations', TranslationController::class);
});
