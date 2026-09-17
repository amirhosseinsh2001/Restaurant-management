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
    Route::get('logout', [AuthController::class, 'logout']);
});

Route::prefix('desk')->middleware(['auth:api', 'isAdmin:admin'])->group(function () {
    Route::get('/', [DeskController::class, 'index']);
    Route::post('/new', [DeskController::class, 'store']);
    Route::patch('/update/{desk}', [DeskController::class, 'update']);
    Route::delete('/delete/{desk}', [DeskController::class, 'destroy']);
});

Route::prefix('category')->middleware(['auth:api', 'isAdmin:admin'])->group(function () {
    Route::get('', [CategoryController::class, 'index']);
    Route::post('/new', [CategoryController::class, 'store']);
    Route::patch('/update/{category}', [CategoryController::class, 'update']);
    Route::delete('/delete/{category}', [CategoryController::class, 'destroy']);
});

Route::prefix('menu')->group(function () {
    Route::get('/', [MenuItemController::class, 'index']);
//    Route::get('/item', [MenuItemController::class, 'index']);
//    Route::get('menu', [MenuItemController::class, 'index']);
    Route::middleware(['auth:api', 'isAdmin:admin'])->group(function () {
        Route::post('/new-item', [MenuItemController::class, 'store']);
        Route::post('/update/{menuItem}', [MenuItemController::class, 'update']);
        Route::delete('/delete/{menuItem}', [MenuItemController::class, 'destroy']);
    });
});

Route::prefix('reservation')->middleware(['auth:api'])->group(function () {
    Route::get('/', [ReservationController::class, 'index']);
    Route::post('/new', [ReservationController::class, 'store']);
    Route::post('/{reservation}/confirmed', [ReservationController::class, 'confirmed'])->middleware(['checkPermission:confirm_reserve']);
    Route::post('/{reservation}/completed', [ReservationController::class, 'completed'])->middleware(['checkPermission:complete_reserve']);
    Route::post('/{reservation}/cancelled', [ReservationController::class, 'cancelled'])->middleware(['checkPermission:cancel_reserve']);
//    Route::post('/update/{reservation}', [ReservationController::class, 'update']);
//    Route::delete('/delete/{reservation}', [ReservationController::class, 'destroy']);
    Route::middleware(['isAdmin:admin'])->group(function () {
//        Route::get('topDesksByRevenue', [ReservationController::class, 'report_best_desk']);
    });
});

Route::prefix('order')->middleware(['auth:api'])->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/new', [OrderController::class, 'store']);
    Route::post('/{order}/served', [OrderController::class, 'serve'])->middleware(['checkPermission:serve_order']);
    Route::post('/{order}/paid', [OrderController::class, 'pay'])->middleware(['checkPermission:pay_order']);
    Route::post('/{order}/cancelled', [OrderController::class, 'cancel'])->middleware(['checkPermission:cancel_order']);
    Route::middleware(['isAdmin:admin'])->group(function () {

    });
});

Route::prefix('report')->middleware(['auth:api', 'isAdmin:admin'])->group(function () {
    Route::get('topDesks', [ReportController::class, 'report_best_desks']);
    Route::get('topMenuItems', [ReportController::class, 'report_best_items']);
});

