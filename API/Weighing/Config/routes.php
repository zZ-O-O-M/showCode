<?php

use App\API\Sections\Weighing\Controllers\AppGroupController;
use App\API\Sections\Weighing\Controllers\AppSessionController;
use App\API\Sections\Weighing\Controllers\AppWeightingsController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:api'], function () {
    Route::prefix('v1')->group(function () {
        Route::prefix('app')->group(function () {
            Route::prefix('weighing')->group(function () {
                Route::prefix('groups')->group(function () {
                    // GET v1/app/weighing/groups
                    Route::get('/', [AppGroupController::class, 'getGroups']);

                    // POST v1/app/weighing/groups/sync
                    Route::post("/sync", [AppGroupController::class, 'sync']);

                    // GET v1/app/weighing/groups/unlinked
                    Route::get("/unlinked", [AppGroupController::class, 'getUnlinked']);

                    // POST v1/app/weighing/groups/link
                    Route::put("/link", [AppGroupController::class, 'link']);

                    // POST v1/app/weighing/groups/weightings
                    Route::post("/weightings", [AppWeightingsController::class, 'loadGroupWeightings']);

                    // GET v1/app/weighing/groups/weightings
                    Route::get("/weightings", [AppWeightingsController::class, 'getGroupWeightings']);
                });

                Route::prefix('sessions')->group(function () {
                    // GET v1/app/weighing/sessions
                    Route::get("/", [AppSessionController::class, 'getSessions']);

                    // POST v1/app/weighing/sessions/sync
                    Route::post("/sync", [AppSessionController::class, 'sync']);

                    // POST v1/app/weighing/sessions/weightings
                    Route::post("/weightings", [AppWeightingsController::class, 'loadSessionWeightings']);

                    // GET v1/app/weighing/sessions/weightings
                    Route::get("/weightings", [AppWeightingsController::class, 'getSessionWeightings']);
                });
            });
        });
    });
});
