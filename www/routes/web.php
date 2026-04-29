<?php

use App\Interfaces\Http\Controllers\Auth\AuthController;
use App\Interfaces\Http\Controllers\DashboardController;
use App\Interfaces\Http\Controllers\UnitSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/unit/switch', [UnitSessionController::class, 'switch'])->name('unit.switch');
    
    // Todas as rotas do painel
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('students', \App\Interfaces\Http\Controllers\StudentController::class);

    // Módulo Financeiro
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/invoices', [\App\Interfaces\Http\Controllers\Finance\InvoiceController::class, 'index'])->name('invoices.index');
        Route::patch('/invoices/{invoice}/pay', [\App\Interfaces\Http\Controllers\Finance\InvoiceController::class, 'pay'])->name('invoices.pay');
    });

    // Módulo Acadêmico / Pedagógico
    Route::prefix('academic')->name('academic.')->group(function () {
        Route::resource('grades', \App\Interfaces\Http\Controllers\Academic\GradeController::class)->except(['show']);
        Route::resource('shifts', \App\Interfaces\Http\Controllers\Academic\ShiftController::class)->except(['show']);
        Route::resource('classes', \App\Interfaces\Http\Controllers\Academic\SchoolClassController::class)->except(['show']);
    });
});
