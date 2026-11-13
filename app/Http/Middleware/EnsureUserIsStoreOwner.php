<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Libera o painel só para donos de loja. O original bloqueava apenas
 * ROLE_USER, então qualquer outro valor (ou nulo) entrava no admin.
 */
class EnsureUserIsStoreOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isOwner()) {
            return redirect()->route('user.orders');
        }

        return $next($request);
    }
}
