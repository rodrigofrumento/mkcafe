<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LoginController extends Controller implements HasMiddleware
{
    use AuthenticatesUsers;

    protected string $redirectTo = '/';

    public static function middleware(): array
    {
        return [
            new Middleware('guest', except: ['logout']),
            new Middleware('auth', only: ['logout']),
        ];
    }

    protected function authenticated(Request $request, User $user): RedirectResponse
    {
        return redirect()->to(self::redirectFor($user));
    }

    /** Para onde mandar o usuário logo após login ou cadastro. */
    public static function redirectFor(User $user): string
    {
        if ($user->isOwner()) {
            return route('admin.stores.index');
        }

        return session()->has('cart') ? route('cart.index') : route('home');
    }
}
