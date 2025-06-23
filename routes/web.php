<?php

use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['locale'])->group(function () {

    Route::get('/', [PageController::class, 'welcome'])->name('welcome');

    Route::middleware(['auth'])->group(function () {

        Route::middleware('verified')->group(function () {

            Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');

        });

        Route::get('/user', [\App\Http\Controllers\UserController::class, 'index'])->name('user.index')->can('view_user');
        Route::get('/user/create', [\App\Http\Controllers\UserController::class, 'create'])->name('user.create')->can('create_user');
        Route::post('/user', [\App\Http\Controllers\UserController::class, 'store'])->name('user.store')->can('create_user');
        Route::get('/user/{user}', [\App\Http\Controllers\UserController::class, 'show'])->name('user.show')->can('view_user');
        Route::get('/user/{user}/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('user.edit')->can('edit_user');
        Route::patch('/user/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('user.update')->can('edit_user');
        Route::delete('/user/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('user.destroy')->can('delete_user');

        Route::get('/role', [\App\Http\Controllers\RoleController::class, 'index'])->name('role.index')->can('view_role');
        Route::get('/role/create', [\App\Http\Controllers\RoleController::class, 'create'])->name('role.create')->can('create_role');
        Route::post('/role', [\App\Http\Controllers\RoleController::class, 'store'])->name('role.store')->can('create_role');
        Route::get('/role/{role}', [\App\Http\Controllers\RoleController::class, 'show'])->name('role.show')->can('view_role');
        Route::get('/role/{role}/edit', [\App\Http\Controllers\RoleController::class, 'edit'])->name('role.edit')->can('edit_role');
        Route::patch('/role/{role}', [\App\Http\Controllers\RoleController::class, 'update'])->name('role.update')->can('edit_role');
        Route::delete('/role/{role}', [\App\Http\Controllers\RoleController::class, 'destroy'])->name('role.destroy')->can('delete_role');

        Route::get('/project', [\App\Http\Controllers\ProjectController::class, 'index'])->name('project.index');
        Route::get('/project/create', [\App\Http\Controllers\ProjectController::class, 'create'])->name('project.create');
        Route::post('/project', [\App\Http\Controllers\ProjectController::class, 'store'])->name('project.store');
        Route::get('/project/{project}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('project.show');
        Route::get('/project/{project}/edit', [\App\Http\Controllers\ProjectController::class, 'edit'])->name('project.edit');
        Route::patch('/project/{project}', [\App\Http\Controllers\ProjectController::class, 'update'])->name('project.update');
        Route::delete('/project/{project}', [\App\Http\Controllers\ProjectController::class, 'destroy'])->name('project.destroy');

        Route::get('/skill', [\App\Http\Controllers\SkillController::class, 'index'])->name('skill.index');
        Route::get('/skill/create', [\App\Http\Controllers\SkillController::class, 'create'])->name('skill.create');
        Route::post('/skill', [\App\Http\Controllers\SkillController::class, 'store'])->name('skill.store');
        Route::get('/skill/{skill}', [\App\Http\Controllers\SkillController::class, 'show'])->name('skill.show');
        Route::get('/skill/{skill}/edit', [\App\Http\Controllers\SkillController::class, 'edit'])->name('skill.edit');
        Route::patch('/skill/{skill}', [\App\Http\Controllers\SkillController::class, 'update'])->name('skill.update');
        Route::delete('/skill/{skill}', [\App\Http\Controllers\SkillController::class, 'destroy'])->name('skill.destroy');

        Route::as('account.')->prefix('account')->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
            Route::get('/change-password', [PasswordController::class, 'edit'])->name('password.edit');
            Route::get('/change-language', [PageController::class, 'locale'])->name('locale');

            Route::get('/activity-log', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('log.index');
            Route::delete('/activity-log', [\App\Http\Controllers\ActivityLogController::class, 'destroyBulk'])->name('log.destroy.bulk');
            Route::delete('/activity-log/{log}', [\App\Http\Controllers\ActivityLogController::class, 'destroy'])->name('log.destroy');
        });
    });

    require __DIR__.'/auth.php';
});
