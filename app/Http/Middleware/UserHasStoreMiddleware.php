<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserHasStoreMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->store()->exists()) {
            flash('Você já possui uma loja')->warning();

            return redirect()->route('admin.stores.index');
        }

        return $next($request);
    }
}
