<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TranslationController;
use App\Http\Controllers\Api\V1\ExportController;

Route::prefix('v1')->middleware(['auth:sanctum', 'throttle'])->group(static function (): void {
    /** Translations **/
    Route::apiResource('translations', TranslationController::class);

    /** Export **/
    Route::get('export/{locale}', [ExportController::class, 'export']);
});
