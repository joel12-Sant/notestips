<?php

namespace App\Services;

use App\Models\User;

class RegisteredUserService
{
    public function createUser(array $data): User
    {
        $user = User::create([
            'username' => $data['username'],
            'password' => $data['password'],
        ]);

        return $user;
    }
}
