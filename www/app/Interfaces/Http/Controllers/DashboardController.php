<?php

namespace App\Interfaces\Http\Controllers;

use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('aluno')) {
            return redirect()->route('student.dashboard');
        }

        if ($user->hasRole('professor')) {
            return redirect()->route('academic.diary.index');
        }

        if ($user->hasRole('secretaria')) {
            return redirect()->route('students.index');
        }

        $unitId = session('active_unit_id');

        // Admin e Diretor caem no dashboard global (filtrado pela unidade ativa)
        $totalStudents = \App\Domains\Enrollment\Models\Student::where('unit_id', $unitId)
            ->where('status', 'active')
            ->count();
            
        $totalRevenue = \App\Domains\Finance\Models\Invoice::where('unit_id', $unitId)
            ->where('status', 'paid')
            ->sum('amount');
            
        $pendingRevenue = \App\Domains\Finance\Models\Invoice::where('unit_id', $unitId)
            ->whereIn('status', ['pending', 'overdue'])
            ->sum('amount');
            
        $overdueRevenue = \App\Domains\Finance\Models\Invoice::where('unit_id', $unitId)
            ->where('status', 'overdue')
            ->sum('amount');
            
        $totalTeachers = \App\Domains\HR\Models\Employee::where('unit_id', $unitId)
            ->where('position', 'like', '%Professor%')
            ->where('is_active', true)
            ->count();

        return view('dashboard', compact('totalStudents', 'totalRevenue', 'pendingRevenue', 'overdueRevenue', 'totalTeachers'));
    }
}
