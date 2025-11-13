<?php

use App\Http\Controllers\api\auth\AuthController;
use App\Http\Controllers\Api\Auth\RolePermissionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Roles
    Route::get('/roles', [RolePermissionController::class, 'roles']);
    Route::post('/roles', [RolePermissionController::class, 'createRole']);
    Route::delete('/roles/{role}', [RolePermissionController::class, 'deleteRole']);
    // Permissions
    Route::get('/permissions', [RolePermissionController::class, 'permissions']);
    Route::post('/permissions', [RolePermissionController::class, 'createPermission']);
    Route::delete('/permissions/{permission}', [RolePermissionController::class, 'deletePermission']);

    // Assign
    Route::post('/roles/{role}/permissions', [RolePermissionController::class, 'assignPermissionsToRole']);
    Route::post('/users/{user}/role', [RolePermissionController::class, 'assignRoleToUser']);
    Route::post('/users/{user}/permission', [RolePermissionController::class, 'assignPermissionToUser']);


    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('/users', UserController::class);
});