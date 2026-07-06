<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rooms
    Route::middleware('role:rooms,auto')->group(function () {
        Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
        Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
        Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
        Route::get('/rooms/{room}/features', [RoomController::class, 'features'])->name('rooms.features');
        Route::put('/rooms/{room}/features', [RoomController::class, 'updateFeatures'])->name('rooms.features.update');
    });

    // Features
    Route::middleware('role:features,auto')->group(function () {
        Route::get('/features', [FeatureController::class, 'index'])->name('features.index');
        Route::post('/features', [FeatureController::class, 'store'])->name('features.store');
        Route::put('/features/{feature}', [FeatureController::class, 'update'])->name('features.update');
        Route::delete('/features/{feature}', [FeatureController::class, 'destroy'])->name('features.destroy');
    });

    // Schedule
    Route::middleware('role:schedule,view')->group(function () {
        Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
    });

    // Bookings
    Route::middleware('role:bookings,auto')->group(function () {
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::get('/bookings/{booking}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
        Route::put('/bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');
        Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    });

    // Logs
    Route::middleware('role:logs,view')->group(function () {
        Route::get('/logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('logs.index');
    });

    // Users
    Route::middleware('role:users,auto')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Permissions (admin only for now)
    Route::middleware('role:permissions,auto')->group(function () {
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    });
});

    // Events
    Route::middleware('role:events,auto')->group(function () {        Route::get('/events', [App\Http\Controllers\EventController::class, 'index'])->name('events.index');        Route::get('/events/create', [App\Http\Controllers\EventController::class, 'create'])->name('events.create');        Route::post('/events', [App\Http\Controllers\EventController::class, 'store'])->name('events.store');        Route::post('/events/{event}/approve', [App\Http\Controllers\EventController::class, 'approve'])->name('events.approve');        Route::post('/events/{event}/reject', [App\Http\Controllers\EventController::class, 'reject'])->name('events.reject');    });
        Route::get('/events/{event}/edit', [App\Http\Controllers\EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [App\Http\Controllers\EventController::class, 'update'])->name('events.update');
        Route::post('/events/{event}/cancel', [App\Http\Controllers\EventController::class, 'cancel'])->name('events.cancel');
require __DIR__.'/auth.php';
