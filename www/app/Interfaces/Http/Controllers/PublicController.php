<?php

namespace App\Interfaces\Http\Controllers;

use App\Domains\Academic\Models\Grade;
use App\Domains\Enrollment\Models\PreEnrollment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PublicController extends Controller
{
    public function index()
    {
        // Se já estiver logado, manda pro Dashboard, caso contrário, Landing Page
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        $grades = Grade::all();
        return view('welcome', compact('grades'));
    }

    public function storeLead(Request $request)
    {
        $validated = $request->validate([
            'parent_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'student_name' => 'required|string|max:255',
            'grade_id' => 'required|exists:grades,id',
            'notes' => 'nullable|string',
        ]);

        // Pegamos a unidade padrao 1 para simplificar o MVP
        $validated['unit_id'] = 1;
        $validated['status'] = 'pending';

        PreEnrollment::create($validated);

        return redirect()->back()->with('success', 'Sua solicitação foi enviada! Em breve nossa secretaria entrará em contato.');
    }
}
