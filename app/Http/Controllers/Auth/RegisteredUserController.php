<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('register');
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'username' => ['required', 'string', 'max:32', 'unique:users,username'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ],
            [
                'username.required' => 'El nombre de usuario es obligatorio.',
                'username.unique' => 'Ese nombre de usuario ya está en uso.',
                'username.max' => 'El nombre de usuario no puede tener más de 32 caracteres.',
                'password.required' => 'La contraseña es obligatoria.',
                'password.confirmed' => 'Las contraseñas no coinciden.',
            ]
        );

        $user = User::create([
            'username' => $request->username,
            'password' => $request->password,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect('/');
    }
}
