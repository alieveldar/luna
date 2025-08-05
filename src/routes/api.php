<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\BuildingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Routes with API Key Authentication
Route::middleware('api.key')->group(function () {
    // Organization routes
    Route::prefix('organizations')->group(function () {
        Route::get('/building/{building_id}', [OrganizationController::class, 'getByBuilding']);
        Route::get('/activity/{activity_id}', [OrganizationController::class, 'getByActivity']);
        Route::get('/nearby', [OrganizationController::class, 'getNearby']);
        Route::get('/area', [OrganizationController::class, 'getInArea']);
        Route::get('/search', [OrganizationController::class, 'search']);
        Route::get('/{id}', [OrganizationController::class, 'show']);
    });

    // Building routes
    Route::get('/buildings', [BuildingController::class, 'index']);
});
