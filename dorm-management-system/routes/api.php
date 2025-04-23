<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\NewsController;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use App\Http\Controllers\Api\Student\StudentController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\Api\GymBookingController;



// Аутентификация
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/password', [AuthController::class, 'setPassword']);
    Route::get('/news', [NewsController::class, 'index']);
});

// Панель студента
Route::middleware(['auth:sanctum', 'role:student'])->prefix('student')->group(function () {
    //ВСЕ ЗАПРОСЫ НА ЖИЛЬЕ
    Route::post('/booking', [BookingController::class, 'store']);
    Route::get('/buildings', [BookingController::class, 'getBuildings']); //Можно переместить вне студента
    Route::get('/floors/{building}', [BookingController::class, 'getFloors']); // Это тоже
    Route::get('/rooms/{building}/{floor}', [BookingController::class, 'getRooms']); // Это тоже4

    //ВСЕ ЗАПРОСЫ НА ДАННЫЕ СТУДЕНТА
    Route::get('/name', [StudentController::class, 'name']);
    Route::get('/data', [StudentController::class, 'allData']);
    Route::get('/habitation', [StudentController::class, 'myRoomInfo']);

    //ВСЕ ЗАПРОСЫ НА ДОКУМЕНТЫ
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::post('/documents', [DocumentController::class, 'upload']);

    //ВСЕ ЗАПРОСЫ НА РЕМОНТ
    Route::get('/requests', [RequestController::class, 'index']);
    Route::post('/request', [RequestController::class, 'create']);
    Route::get('/request/{id}', [RequestController::class, 'show']);
    Route::put('/request/{repairRequest}', [RequestController::class, 'update']);
    Route::delete('/request/{repairRequest}', [RequestController::class, 'destroy']);
    Route::get('/request/{id}/edit', [RequestController::class, 'edit']);
    Route::get('/requests', [RequestController::class, 'index']);


    //ВСЕ ЗАПРОСЫ НА ФИЗРУ
    Route::get('gym-bookings', [GymBookingController::class, 'index']);
    Route::get('sports-page', [GymBookingController::class, 'showSportsPage']);
    Route::post('gym-booking', [GymBookingController::class, 'store']);
    Route::post('gym-booking/{gymBooking}/confirm', [GymBookingController::class, 'confirm']);
    Route::delete('gym-booking', [GymBookingController::class, 'cancel']);
    Route::post('recovery', [GymBookingController::class, 'recovery']);
    Route::delete('recovery/{recoveryId}', [GymBookingController::class, 'cancelRecovery']);


});


// Панель менеджера
Route::middleware(['auth:sanctum', 'role:manager'])->prefix('manager')->group(function () {
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/booking/reject/{id}', [BookingController::class, 'reject']);
    Route::post('/booking/accept/{id}', [BookingController::class, 'accepted']);
});

