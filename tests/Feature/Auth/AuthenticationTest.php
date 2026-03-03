<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

function crearUsuarioAuth(string $username = 'tester', string $password = 'Password123!'): User
{
    return User::create([
        'username' => $username,
        'password' => $password,
    ]);
}

test('muestra login y registro para usuarios invitados', function () {
    $this->get('/auth/login')->assertOk();
    $this->get('/auth/register')->assertOk();
});

test('registra un usuario y crea una nota de bienvenida', function () {
    $response = $this->post('/auth/register', [
        'username' => 'joel',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertRedirect('/auth/login');

    $user = User::where('username', 'joel')->first();

    expect($user)->not->toBeNull();
    expect($user->notes()->count())->toBe(1);
    expect($user->notes()->first()->title)->toBe('Bienvenido a NotesTips');
});

test('valida el payload de registro', function () {
    $this->post('/auth/register', [
        'username' => '',
        'password' => 'short',
        'password_confirmation' => 'different',
    ])->assertSessionHasErrors(['username', 'password']);
});

test('inicia sesion con credenciales validas', function () {
    $user = crearUsuarioAuth('alice', 'Password123!');

    $this->post('/auth/login', [
        'username' => 'alice',
        'password' => 'Password123!',
    ])->assertRedirect('/notes');

    $this->assertAuthenticatedAs($user);
});

test('rechaza el inicio de sesion con credenciales invalidas', function () {
    crearUsuarioAuth('bob', 'Password123!');

    $this->post('/auth/login', [
        'username' => 'bob',
        'password' => 'WrongPassword!',
    ])->assertSessionHasErrors(['username']);

    $this->assertGuest();
});

test('bloquea temporalmente el login tras demasiados intentos fallidos', function () {
    crearUsuarioAuth('locked-user', 'Password123!');

    $key = 'locked-user|127.0.0.1';
    RateLimiter::clear($key);

    for ($i = 0; $i < 5; $i++) {
        $this->post('/auth/login', [
            'username' => 'locked-user',
            'password' => 'WrongPassword!',
        ])->assertSessionHasErrors(['username']);
    }

    $this->post('/auth/login', [
        'username' => 'locked-user',
        'password' => 'WrongPassword!',
    ])->assertSessionHasErrors(['username'])
        ->assertSessionHas('lock_seconds');

    expect(session('lock_seconds'))->toBeInt()->toBeGreaterThan(0);
});

test('cierra sesion de un usuario autenticado', function () {
    $user = crearUsuarioAuth('logout-user', 'Password123!');

    $this->actingAs($user)
        ->post('/auth/logout')
        ->assertRedirect('/');

    $this->assertGuest();
});

test('redirige invitados fuera de las rutas de notas', function () {
    $this->get('/notes')->assertRedirect('/auth/login');
});
