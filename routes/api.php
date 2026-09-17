<?php


use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\CategoryController;
use App\Http\Controllers\v1\DeskController;
use App\Http\Controllers\v1\MenuItemController;
use App\Http\Controllers\v1\OrderController;
use App\Http\Controllers\v1\ReportController;
use App\Http\Controllers\v1\ReservationController;
use Illuminate\Support\Facades\Route;




Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('auth-register');
    Route::post('verify-register', [AuthController::class, 'verify_register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('verify-login', [AuthController::class, 'verifyLogin']);
});

Route::prefix('profile')->middleware(['auth:api'])->group(function () {
    Route::post('/', [AuthController::class, 'profile']);
    Route::patch('update', [AuthController::class, 'update']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::prefix('desks')->middleware(['auth:api', 'isAdmin:admin'])->group(function () {
    Route::get('/', [DeskController::class, 'index']);
    Route::post('/', [DeskController::class, 'store']);
    Route::patch('/{desk}', [DeskController::class, 'update']);
    Route::delete('/{desk}', [DeskController::class, 'destroy']);
});

Route::prefix('categories')->middleware(['auth:api', 'isAdmin:admin'])->group(function () {
    Route::get('', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::patch('/{category}', [CategoryController::class, 'update']);
    Route::delete('/{category}', [CategoryController::class, 'destroy']);
});

Route::prefix('menus')->group(function () {
    Route::get('/', [MenuItemController::class, 'index']);
    Route::middleware(['auth:api', 'isAdmin:admin'])->group(function () {
        Route::post('/', [MenuItemController::class, 'store']);
        Route::post('/{menuItem}', [MenuItemController::class, 'update']);
        Route::delete('/{menuItem}', [MenuItemController::class, 'destroy']);
    });
});

Route::prefix('reservations')->middleware(['auth:api'])->group(function () {
    Route::get('/', [ReservationController::class, 'index']);
    Route::post('/', [ReservationController::class, 'store']);
    Route::post('/{reservation}/confirmed', [ReservationController::class, 'confirmed'])->middleware(['checkPermission:confirm_reserve']);
    Route::post('/{reservation}/completed', [ReservationController::class, 'completed'])->middleware(['checkPermission:complete_reserve']);
    Route::post('/{reservation}/cancelled', [ReservationController::class, 'cancelled'])->middleware(['checkPermission:cancel_reserve']);
    Route::middleware(['isAdmin:admin'])->group(function () {
    });
});

Route::prefix('orders')->middleware(['auth:api'])->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::post('/{order}/served', [OrderController::class, 'serve'])->middleware(['checkPermission:serve_order']);
    Route::post('/{order}/paid', [OrderController::class, 'pay'])->middleware(['checkPermission:pay_order']);
    Route::post('/{order}/cancelled', [OrderController::class, 'cancel'])->middleware(['checkPermission:cancel_order']);
    Route::middleware(['isAdmin:admin'])->group(function () {

    });
});

Route::prefix('reports')->middleware(['auth:api', 'isAdmin:admin'])->group(function () {
    Route::post('topDesks', [ReportController::class, 'report_best_desks']);
    Route::post('topMenuItems', [ReportController::class, 'report_best_items']);
});

