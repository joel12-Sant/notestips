<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisteredUserStoreRequest;
use App\Services\RegisteredUserService;
use Illuminate\Auth\Events\Registered;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('register');
    }

    public function store(RegisteredUserStoreRequest $request, RegisteredUserService $registeredUserService)
    {
        $user = $registeredUserService->createUser($request->validated());

        event(new Registered($user));

        return redirect('/auth/login');
    }
}
