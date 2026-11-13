<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\UserRegisteredEmail;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller implements HasMiddleware
{
    use RegistersUsers;

    protected string $redirectTo = '/';

    public static function middleware(): array
    {
        return [new Middleware('guest')];
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
    }

    protected function create(array $data): User
    {
        // Todo cadastro público vira cliente; donos de loja são promovidos manualmente.
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
    }

    protected function registered(Request $request, User $user): RedirectResponse
    {
        Mail::to($user->email)->send(new UserRegisteredEmail($user));

        return redirect()->to(LoginController::redirectFor($user));
    }
}
