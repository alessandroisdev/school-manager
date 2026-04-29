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
        return view('dashboard');
    }
}
