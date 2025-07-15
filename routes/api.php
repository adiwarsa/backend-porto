<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SkillController;

// Webhook routes - no authentication required
Route::match(['GET', 'POST'], 'webhook/fonnte', [WebhookController::class, 'handleFonnteWebhook']);
Route::get('webhook/test', [WebhookController::class, 'test']);

Route::middleware('api.token')->group(function () {
    // Project routes
    Route::get('projects', [ProjectController::class, 'index']);
    Route::get('projects/{project}', [ProjectController::class, 'show']);
    Route::post('projects', [ProjectController::class, 'store']);
    Route::put('projects/{project}', [ProjectController::class, 'update']);
    Route::delete('projects/{project}', [ProjectController::class, 'destroy']);

    // Skill routes
    Route::get('skills', [SkillController::class, 'index']);
});
