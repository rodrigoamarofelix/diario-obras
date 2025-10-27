<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckProfilePermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$profiles): mixed
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Verifica se o usuário tem um dos perfis permitidos
        foreach ($profiles as $profile) {
            if ($user->profile === $profile) {
                return $next($request);
            }
        }

        abort(403, 'Você não tem permissão para acessar esta página.');
    }
}

