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

        // Admin e Diretor caem no dashboard global
        $totalStudents = \App\Domains\Enrollment\Models\Enrollment::where('status', 'active')->count();
        $totalRevenue = \App\Domains\Finance\Models\Invoice::where('status', 'paid')->sum('amount');
        $pendingRevenue = \App\Domains\Finance\Models\Invoice::whereIn('status', ['pending', 'overdue'])->sum('amount');
        $overdueRevenue = \App\Domains\Finance\Models\Invoice::where('status', 'overdue')->sum('amount');
        $totalTeachers = \App\Domains\HR\Models\Teacher::count();

        return view('dashboard', compact('totalStudents', 'totalRevenue', 'pendingRevenue', 'overdueRevenue', 'totalTeachers'));
    }
}
