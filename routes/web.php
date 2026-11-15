<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\ManagerController;
use Illuminate\Support\Facades\Route;

// =========================================================================
// 1. ПУБЛИЧНЫЕ МАРШРУТЫ (Доступны всем посетителям и гостям)
// =========================================================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/catalog', [HouseController::class, 'catalog'])->name('catalog');
Route::get('/houses/{slug}', [HouseController::class, 'show'])->name('houses.show');

// Киллер-фича для сдачи: Быстрый вход для преподавателя в 1 клик
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quick-login');

// =========================================================================
// 2. ГОСТЕВЫЕ МАРШРУТЫ (Только для неавторизованных пользователей)
// =========================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// =========================================================================
// 3. ЗОНА АВТОРИЗОВАННОГО КЛИЕНТА (Требуется сессия auth)
// =========================================================================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Оформление бронирования и личный кабинет
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('client.bookings');
    Route::post('/my-bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('client.bookings.cancel');
});

// =========================================================================
// 4. РАБОЧЕЕ МЕСТО МЕНЕДЖЕРА (Доступ: менеджер и администратор)
// =========================================================================
Route::middleware(['auth', 'role:manager,admin'])->group(function () {
    Route::get('/manager', [ManagerController::class, 'index'])->name('manager.index');
    Route::patch('/manager/bookings/{booking}/status', [ManagerController::class, 'updateStatus'])->name('manager.bookings.status');
});

// =========================================================================
// 5. ПАНЕЛЬ АДМИНИСТРАТОРА (Доступ ТОЛЬКО: администратор)
// =========================================================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::patch('/admin/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('admin.users.role');
});
