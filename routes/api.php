<?php
use App\Http\Controllers\RoleHistoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Middleware\CheckPermission;
use App\Http\Controllers\PermissionHistoryController;
use App\Http\Controllers\UserHistoryController;
use App\Http\Controllers\ChangeLogController;
use App\Http\Controllers\TwoFactorAuthController;
use App\Http\Middleware\CheckAbility;
use illuminate\http\Request;
use App\Http\Controllers\DeployController;

Route::prefix('auth')->group(function () {
    Route::middleware('guest')->post('/login', [AuthController::class, 'login']);
    Route::middleware('guest')->post('/register', [AuthController::class, 'register']); 

    Route::middleware(['auth:sanctum', CheckAbility::class . ':*'])->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/out', [AuthController::class, 'logout']);
        Route::get('/tokens', [AuthController::class, 'tokens']);
        Route::post('/out_all', [AuthController::class, 'logoutAll']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });
});

Route::prefix('ref/user')
    ->middleware(['auth:sanctum', CheckPermission::class . ':get-list-user'])
    ->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('{user}/role', [UserRoleController::class, 'index']);

        Route::middleware(CheckPermission::class . ':manage-role')->group(function () {
            Route::post('{user}/role', [UserRoleController::class, 'store']);
            Route::delete('{user}/role/{pivot}', [UserRoleController::class, 'destroy']);
            Route::delete('{user}/role/{pivot}/soft', [UserRoleController::class, 'softDelete']);
            Route::patch('{user}/role/{pivot}/restore', [UserRoleController::class, 'restore']);
        });
    });

Route::prefix('ref/policy/role')
    ->middleware(['auth:sanctum', CheckPermission::class . ':get-list-role'])
    ->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::get('{id}', [RoleController::class, 'show']);

        Route::middleware(CheckPermission::class . ':manage-role')->group(function () {
            Route::post('/', [RoleController::class, 'store']);
            Route::put('{id}', [RoleController::class, 'update']);
            Route::delete('{id}', [RoleController::class, 'destroy']);
            Route::delete('{id}/soft', [RoleController::class, 'softDelete']);
            Route::patch('{id}/restore', [RoleController::class, 'restore']);
        });
    });

Route::prefix('ref/policy/permission')
    ->middleware(['auth:sanctum', CheckPermission::class . ':get-list-permission'])
    ->group(function () {
        Route::get('/', [PermissionController::class, 'index']);
        Route::get('{id}', [PermissionController::class, 'show']);

        Route::middleware(CheckPermission::class . ':manage-permission')->group(function () {
            Route::post('/', [PermissionController::class, 'store']);
            Route::put('{id}', [PermissionController::class, 'update']);
            Route::delete('{id}', [PermissionController::class, 'destroy']);
            Route::delete('{id}/soft', [PermissionController::class, 'softDelete']);
            Route::patch('{id}/restore', [PermissionController::class, 'restore']);
        });
          });

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/ref/change-logs/users', [ChangeLogController::class, 'getUserLogs'])
        ->middleware(CheckPermission::class . ':get-story-user');
    Route::get('/ref/change-logs/roles', [ChangeLogController::class, 'getRoleLogs'])
        ->middleware(CheckPermission::class . ':get-story-role');
        Route::get('/ref/change-logs/roles/{entity_id}', [ChangeLogController::class, 'getRoleLogsByEntity']);
    Route::get('/ref/change-logs/permissions', [ChangeLogController::class, 'getPermissionLogs'])
        ->middleware(CheckPermission::class . ':get-story-permission');

        Route::middleware(['auth:sanctum', CheckPermission::class . ':rollback-change'])
    ->post('/ref/change-logs/rollback/{logId}', [ChangeLogController::class, 'rollbackChange']);
});

Route::middleware('auth:sanctum,ability:2fa')->group(function () {
    Route::post('/2fa/request-code', [TwoFactorAuthController::class, 'requestCode']);
    Route::post('/2fa/confirm-code', [TwoFactorAuthController::class, 'verifyCode']);
});

Route::middleware('auth:sanctum')->post('/2fa/toggle', [TwoFactorAuthController::class, 'toggle2FA']);

Route::post('/hooks/git', [DeployController::class,'deploy']);