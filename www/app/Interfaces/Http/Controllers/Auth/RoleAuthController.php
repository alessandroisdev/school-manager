<?php

namespace App\Interfaces\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class RoleAuthController extends Controller
{
    public function showStudentLogin()
    {
        return view('auth.login-student', ['role' => 'aluno']);
    }

    public function showParentLogin()
    {
        return view('auth.login-parent', ['role' => 'responsavel']);
    }

    public function showTeacherLogin()
    {
        return view('auth.login-teacher', ['role' => 'professor']);
    }

    public function showAdminLogin()
    {
        return view('auth.login-admin', ['role' => 'admin']);
    }

    public function authenticate(Request $request, $role)
    {
        // Aceita tanto username (Matrícula/CPF) quanto email
        $loginField = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $request->input('login'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Validação de segurança: garantir que o usuário pertence à role do painel
            // Para admin, aceitamos admin, diretor ou secretaria.
            $allowedRoles = [];
            if ($role === 'aluno') $allowedRoles = ['aluno'];
            if ($role === 'responsavel') $allowedRoles = ['responsavel'];
            if ($role === 'professor') $allowedRoles = ['professor'];
            if ($role === 'admin') $allowedRoles = ['admin', 'diretor', 'secretaria'];

            $hasAllowedRole = false;
            foreach ($allowedRoles as $allowedRole) {
                if ($user->hasRole($allowedRole)) {
                    $hasAllowedRole = true;
                    break;
                }
            }

            if (!$hasAllowedRole) {
                Auth::logout();
                return back()->withErrors([
                    'login' => 'Este usuário não possui acesso a este painel.',
                ])->onlyInput('login');
            }

            $request->session()->regenerate();
            
            if ($user->units->isNotEmpty()) {
                session(['active_unit_id' => $user->units->first()->id]);
            }

            // Redirecionamento por papel principal
            if ($user->hasRole('aluno')) {
                return redirect()->intended('student/portal');
            }
            if ($user->hasRole('responsavel')) {
                return redirect()->intended('dashboard'); // TODO: Create parent portal later if needed
            }

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'login' => 'As credenciais fornecidas não conferem com nossos registros.',
        ])->onlyInput('login');
    }
}
