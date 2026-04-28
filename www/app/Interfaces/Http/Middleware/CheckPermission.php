<?php

namespace App\Interfaces\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();
        if (!$user || !$user->can($permission)) {
            abort(403, 'Acesso Negado: Você não possui a permissão necessária.');
        }

        return $next($request);
    }
}
