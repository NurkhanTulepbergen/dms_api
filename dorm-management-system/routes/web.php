<?php
/*
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Web\Admin\AdminController;
use App\Http\Controllers\Web\Admin\AdminUserController;
use App\Http\Controllers\Web\Admin\NewsAdminController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\BookingController;
use App\Http\Controllers\Web\DocumentController;
use App\Http\Controllers\Web\Employee\EmployeeController;
use App\Http\Controllers\Web\GymBookingController;
use App\Http\Controllers\Web\LanguageController;
use App\Http\Controllers\Web\Manager\ManagerController;
use App\Http\Controllers\Web\Manager\NewsManagerController;
use App\Http\Controllers\Web\RequestController;
use App\Http\Controllers\Web\Student\StudentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

//    // Оплата
//    Route::post('/payment', [PaymentController::class, 'store'])->name('payment.store');
//

Route::middleware(['auth','admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/dashboard/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/dashboard/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/dashboard/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::resource('/news', NewsAdminController::class)->names('admin.news');
    Route::delete('/dashboard/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/users/{id}/json', [AdminUserController::class, 'getUserJson']);
});

// Менеджерская панель
Route::middleware(['auth','role:manager'])->prefix('manager')->group(function () {
    Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('manager.dashboard');
    Route::get('/dashboard/requests', [BookingController::class, 'indexForManager'])->name('manager.requests');
    Route::get('/dashboard/requests/{id}/accept', [BookingController::class, 'accept'])->name('booking.accept');
    Route::get('/dashboard/requests/{id}/reject', [BookingController::class, 'reject'])->name('booking.reject');
    Route::resource('/dashboard/news', NewsManagerController::class)->names('manager.news');
    Route::put('/dashboard/news/{news}', [NewsManagerController::class, 'update'])->name('manager.news.update');


});

// Студенческая панель
Route::middleware(['auth','role:student'])->prefix('student')->group(function (){
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/personal', [StudentController::class, 'personal'])->name('student.personal');
    Route::post('/personal/profile/update', [StudentController::class, 'updateProfile', 'update'])->name('student.profile.update');
    Route::patch('/personal/profile/update', [StudentController::class, 'update'])->name('student.profile.update');
    Route::get('/personal/floors/{building_id}', [BookingController::class, 'getFloors'])->name('booking.getFloors');
    Route::get('/personal/rooms/{building_id}/{floor}', [BookingController::class, 'getRooms'])->name('booking.getRooms');
    Route::post('/document/upload', [DocumentController::class, 'store'])->name('document.upload');
    Route::post('personal/document/upload', [DocumentController::class, 'upload'])->name('document.upload');
    Route::post('/personal/booking/store', [BookingController::class, 'store'])->name('booking.store');
    Route::post('/personal/booking/change-room', [BookingController::class, 'changeRoom'])->name('booking.changeRoom');
    Route::post('/personal/sports/store', [GymBookingController::class, 'store'])->name('sports.store');
    Route::delete('/personal/sports', [GymBookingController::class, 'cancel'])->name('sports.cancel');
    Route::post('/personal/sports/recovery', [GymBookingController::class, 'recovery'])->name('sports.recovery');
    Route::delete('/personal/sports/recovery/{recovery}', [GymBookingController::class, 'cancelRecovery'])->name('sports.recovery.cancel');
    Route::get('/personal/sports', [GymBookingController::class, 'showSportsPage'])->name('sports.page');
    Route::get('/refresh-user', function () {
        Auth::user()->refresh();
        return redirect()->route('student.personal')
            ->with('successType', 'user_updated')
            ->with('success', 'Пользователь обновлен!');
    })->name('refresh.user');
    Route::get('/personal/create-request', [RequestController::class, 'create'])->name('request.create');
    Route::post('/personal', [RequestController::class, 'store'])->name('request.store');
    Route::get('/personal/requests', [RequestController::class, 'index'])->name('request.index');
    Route::get('/personal/requests/{id}', [RequestController::class, 'show'])->name('request.show');
    Route::get('/personal/requests/{id}/edit', [RequestController::class, 'edit'])->name('request.edit');
    Route::put('/personal/requests/{repairRequest}', [RequestController::class, 'update'])->name('request.update');
    Route::delete('/personal/requests/{repairRequest}', [RequestController::class, 'destroy'])->name('request.destroy');
});

// Employee dashboard
    Route::middleware(['auth','role:employee'])->prefix('employee')->group(function (){
        Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('employee.dashboard');
        Route::post('/updateProfile', [EmployeeController::class, 'updateProfile'])->name('employee.updateProfile');
        Route::patch('/updatePassword', [EmployeeController::class, 'updatePassword'])->name('employee.updatePassword');
        Route::get('/dashboard/requests', [EmployeeController::class, 'requests'])->name('employee.requests');
        Route::get('/dashboard/requests/{id}', [EmployeeController::class, 'show'])->name('employee.request.show');
        Route::get('/dashboard/requests/{id}/edit', [EmployeeController::class, 'edit'])->name('employee.request.edit');
        Route::put('/dashboard/requests/{id}', [EmployeeController::class, 'update'])->name('employee.request.update');
});
    Route::middleware(['language'])->group(function (){
        Route::post('/language-switch', [LanguageController::class, 'languageSwitch'])->name('language.switch');
    });*/
