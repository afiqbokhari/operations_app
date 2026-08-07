<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FrontDeskItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/module/switch', [ModuleController::class, 'switch'])->name('module.switch');

    // Front Desk module
    Route::prefix('front-desk')->name('front-desk.')->group(function () {
        Route::get('/dashboard', [FrontDeskItemController::class, 'dashboard'])->middleware('permission:front_desk.view')->name('dashboard');

        // Legal Mail Log - MUST be before /mail/{frontDeskItem}
        Route::get('/mail/legal', [FrontDeskItemController::class, 'legalIndex'])->middleware('permission:front_desk.view')->name('mail.legal.index');
        Route::post('/mail/legal/batch-collect', [FrontDeskItemController::class, 'legalBatchCollect'])->middleware('permission:front_desk.edit')->name('mail.legal.batch-collect');
        Route::post('/mail/legal/{frontDeskItem}/collect', [FrontDeskItemController::class, 'legalCollect'])->middleware('permission:front_desk.edit')->name('mail.legal.collect');

        // Mail Log
        Route::get('/mail', [FrontDeskItemController::class, 'index'])->middleware('permission:front_desk.view')->name('mail.index');
        Route::get('/mail/create', [FrontDeskItemController::class, 'create'])->middleware('permission:front_desk.create')->name('mail.create');
        Route::get('/mail/{frontDeskItem}', [FrontDeskItemController::class, 'show'])->middleware('permission:front_desk.view')->name('mail.show');
        Route::post('/mail', [FrontDeskItemController::class, 'store'])->middleware('permission:front_desk.create')->name('mail.store');
        Route::get('/mail/{frontDeskItem}/edit', [FrontDeskItemController::class, 'edit'])->middleware('permission:front_desk.edit')->name('mail.edit');
        Route::put('/mail/{frontDeskItem}', [FrontDeskItemController::class, 'update'])->middleware('permission:front_desk.edit')->name('mail.update');
        Route::delete('/mail/{frontDeskItem}', [FrontDeskItemController::class, 'destroy'])->middleware('permission:front_desk.delete')->name('mail.destroy');
        Route::post('/mail/{frontDeskItem}/pass', [FrontDeskItemController::class, 'pass'])->middleware('permission:front_desk.edit')->name('mail.pass');
        Route::post('/mail/batch-pass', [FrontDeskItemController::class, 'batchPass'])->middleware('permission:front_desk.edit')->name('mail.batch-pass');
        Route::post('/mail/{frontDeskItem}/undo-pass', [FrontDeskItemController::class, 'undoPass'])->middleware('permission:front_desk.edit')->name('mail.undo-pass');
    });

    // Booking module
    Route::prefix('booking')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->middleware(['verified', 'permission:bookings.view'])->name('dashboard');

        // Rooms
        Route::get('/rooms', [RoomController::class, 'index'])->middleware('permission:rooms.view')->name('rooms.index');
        Route::post('/rooms', [RoomController::class, 'store'])->middleware('permission:rooms.create')->name('rooms.store');
        Route::put('/rooms/{room}', [RoomController::class, 'update'])->middleware('permission:rooms.edit')->name('rooms.update');
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->middleware('permission:rooms.delete')->name('rooms.destroy');
        Route::get('/rooms/{room}/features', [RoomController::class, 'features'])->middleware('permission:rooms.edit')->name('rooms.features');
        Route::put('/rooms/{room}/features', [RoomController::class, 'updateFeatures'])->middleware('permission:rooms.edit')->name('rooms.features.update');

        // Features
        Route::get('/features', [FeatureController::class, 'index'])->middleware('permission:features.view')->name('features.index');
        Route::post('/features', [FeatureController::class, 'store'])->middleware('permission:features.create')->name('features.store');
        Route::put('/features/{feature}', [FeatureController::class, 'update'])->middleware('permission:features.edit')->name('features.update');
        Route::delete('/features/{feature}', [FeatureController::class, 'destroy'])->middleware('permission:features.delete')->name('features.destroy');

        // Schedule
        Route::get('/schedule', [ScheduleController::class, 'index'])->middleware('permission:schedule.view')->name('schedule.index');
        Route::get('/schedule/print-week', [ScheduleController::class, 'printWeek'])->middleware('permission:schedule.view')->name('schedule.print-week');

        // Bookings
        Route::get('/bookings', [BookingController::class, 'index'])->middleware('permission:bookings.view')->name('bookings.index');
        Route::get('/bookings/create', [BookingController::class, 'create'])->middleware('permission:bookings.create')->name('bookings.create');
        Route::post('/bookings', [BookingController::class, 'store'])->middleware('permission:bookings.create')->name('bookings.store');
        Route::get('/bookings/{booking}/edit', [BookingController::class, 'edit'])->middleware('permission:bookings.edit')->name('bookings.edit');
        Route::put('/bookings/{booking}', [BookingController::class, 'update'])->middleware('permission:bookings.edit')->name('bookings.update');
        Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->middleware('permission:bookings.delete')->name('bookings.destroy');

        // Events
        Route::get('/events', [EventController::class, 'index'])->middleware('permission:events.view')->name('events.index');
        Route::get('/events/create', [EventController::class, 'create'])->middleware('permission:events.create')->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->middleware('permission:events.create')->name('events.store');
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->middleware('permission:events.edit')->name('events.edit');
        Route::put('/events/{event}', [EventController::class, 'update'])->middleware('permission:events.edit')->name('events.update');
        Route::post('/events/{event}/approve', [EventController::class, 'approve'])->middleware('permission:events.edit')->name('events.approve');
        Route::post('/events/{event}/reject', [EventController::class, 'reject'])->middleware('permission:events.edit')->name('events.reject');
        Route::post('/events/{event}/cancel', [EventController::class, 'cancel'])->middleware('permission:events.edit')->name('events.cancel');
    });

    // Admin module
    Route::prefix('admin')->group(function () {
        // Admin Dashboard
        Route::get('/', [AdminController::class, 'index'])->middleware('permission:users.view|permissions.view|logs.view|menus.view')->name('admin.dashboard');

        // Logs
        Route::get('/logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->middleware('permission:logs.view')->name('logs.index');

        // Users
        Route::get('/users', [UserController::class, 'index'])->middleware('permission:users.view')->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->middleware('permission:users.create')->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:users.edit')->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('users.destroy');

        // Roles
        Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:users.view')->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:users.create')->name('roles.store');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:users.delete')->name('roles.destroy');

        // Permissions
        Route::get('/permissions', [PermissionController::class, 'index'])->middleware('permission:permissions.view')->name('permissions.index');
        Route::put('/permissions/{role}', [PermissionController::class, 'update'])->middleware('permission:permissions.edit')->name('permissions.update');

        // Menus
        Route::get('/menus', [MenuController::class, 'index'])->middleware('permission:menus.view')->name('menus.index');
        Route::post('/menus', [MenuController::class, 'store'])->middleware('permission:menus.create')->name('menus.store');
        Route::put('/menus/{menu}', [MenuController::class, 'update'])->middleware('permission:menus.edit')->name('menus.update');
        Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->middleware('permission:menus.delete')->name('menus.destroy');
    });
});

require __DIR__.'/auth.php';
