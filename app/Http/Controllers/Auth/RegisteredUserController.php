<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisteredUserStoreRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('register');
    }

    public function store(RegisteredUserStoreRequest $request)
    {
        $user = User::create([
            'username' => $request->username,
            'password' => $request->password,
        ]);

        event(new Registered($user));

        return redirect('/auth/login');
    }
}
