<?php

namespace App\Interfaces\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckUnitContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('current_unit_id')) {
            $user = $request->user();
            if ($user && $user->units->isNotEmpty()) {
                Session::put('current_unit_id', $user->units->first()->id);
            } else {
                abort(403, 'Contexto de Unidade não definido.');
            }
        }

        return $next($request);
    }
}
