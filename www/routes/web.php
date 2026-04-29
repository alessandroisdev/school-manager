<?php

use App\Interfaces\Http\Controllers\Auth\AuthController;
use App\Interfaces\Http\Controllers\DashboardController;
use App\Interfaces\Http\Controllers\UnitSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Interfaces\Http\Controllers\PublicController::class, 'index'])->name('welcome');
Route::post('/lead/store', [\App\Interfaces\Http\Controllers\PublicController::class, 'storeLead'])->name('public.lead.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/unit/switch', [UnitSessionController::class, 'switch'])->name('unit.switch');
    
    // Todas as rotas do painel
    Route::get('/dashboard', [\App\Interfaces\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Portal do Aluno
    Route::get('/student/portal', [\App\Interfaces\Http\Controllers\StudentPortalController::class, 'index'])->name('student.dashboard');

    // Módulo Financeiro
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/invoices', [\App\Interfaces\Http\Controllers\Finance\InvoiceController::class, 'index'])->name('invoices.index');
        Route::patch('/invoices/{invoice}/pay', [\App\Interfaces\Http\Controllers\Finance\InvoiceController::class, 'pay'])->name('invoices.pay');
    });

    // Context Switcher (Alternar entre escolas)
    Route::post('/context/switch', [\App\Interfaces\Http\Controllers\Admin\ContextController::class, 'switch'])->name('context.switch');

    // Administração Global (Super Admin)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('units', \App\Interfaces\Http\Controllers\Admin\UnitController::class)->except(['show']);
        
        // Configurações da Unidade
        Route::get('settings', [\App\Interfaces\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Interfaces\Http\Controllers\Admin\SettingController::class, 'store'])->name('settings.store');
    });

    // Módulo Secretaria
    Route::prefix('secretariat')->group(function () {
        // CRM Captação
        Route::get('leads', [\App\Interfaces\Http\Controllers\Secretariat\LeadController::class, 'index'])->name('secretariat.leads.index');
        Route::post('leads/{lead}/approve', [\App\Interfaces\Http\Controllers\Secretariat\LeadController::class, 'approve'])->name('secretariat.leads.approve');
        Route::post('leads/{lead}/reject', [\App\Interfaces\Http\Controllers\Secretariat\LeadController::class, 'reject'])->name('secretariat.leads.reject');

        Route::resource('students', \App\Interfaces\Http\Controllers\StudentController::class);
        Route::resource('enrollments', \App\Interfaces\Http\Controllers\EnrollmentController::class)->except(['show', 'edit', 'update']);
    });

    // Módulo Acadêmico / Pedagógico
    Route::prefix('academic')->name('academic.')->group(function () {
        
        // Smart Tools (IA)
        Route::get('smart-tools', [\App\Interfaces\Http\Controllers\Academic\SmartAssignmentController::class, 'index'])->name('smart.index');
        Route::post('smart-tools/auto-enroll', [\App\Interfaces\Http\Controllers\Academic\SmartAssignmentController::class, 'autoEnroll'])->name('smart.autoEnroll');

        Route::resource('grades', \App\Interfaces\Http\Controllers\Academic\GradeController::class)->except(['show']);
        Route::resource('shifts', \App\Interfaces\Http\Controllers\Academic\ShiftController::class)->except(['show']);
        Route::resource('classes', \App\Interfaces\Http\Controllers\Academic\SchoolClassController::class)->except(['show']);
        Route::resource('subjects', \App\Interfaces\Http\Controllers\Academic\SubjectController::class)->except(['show']);
        Route::resource('assignments', \App\Interfaces\Http\Controllers\Academic\TeacherAssignmentController::class)->except(['show', 'edit', 'update']);

        // Diário de Classe (Professor)
        Route::prefix('diary')->name('diary.')->group(function () {
            Route::get('/', [\App\Interfaces\Http\Controllers\Academic\TeacherPortalController::class, 'index'])->name('index');
            
            // Frequência
            Route::get('/{assignment}/lessons', [\App\Interfaces\Http\Controllers\Academic\LessonController::class, 'index'])->name('lessons');
            Route::post('/{assignment}/lessons', [\App\Interfaces\Http\Controllers\Academic\LessonController::class, 'store'])->name('lessons.store');
            
            // Notas
            Route::get('/{assignment}/evaluations', [\App\Interfaces\Http\Controllers\Academic\EvaluationController::class, 'index'])->name('evaluations');
            Route::post('/{assignment}/evaluations', [\App\Interfaces\Http\Controllers\Academic\EvaluationController::class, 'store'])->name('evaluations.store');
        });
    });

    // Módulo de Recursos Humanos (Colaboradores e Professores)
    Route::prefix('hr')->name('hr.')->group(function () {
        Route::resource('employees', \App\Interfaces\Http\Controllers\HR\EmployeeController::class)->except(['show']);
        Route::resource('teachers', \App\Interfaces\Http\Controllers\HR\TeacherController::class)->except(['show']);
    });
});
