@extends('layout.app')

@section('content')
    <h1>Registro</h1>

    <form id="registerForm" method="POST" action="{{ route('register.store') }}">
        @csrf

        <label for="username">Username</label><br>
        <input id="username" type="text" name="username" value="{{ old('username') }}" required maxlength="32">
        @error('username')
            <p>{{ $message }}</p>
        @enderror
        <br><br>

        <label for="password">Password</label><br>
        <input id="password" type="password" name="password" required autocomplete="new-password">
        <button type="button" id="toggle1">Mostrar</button>
        <div id="passMsg"></div>
        <br><br>

        <label for="password_confirmation">Confirmar password</label><br>
        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
        <button type="button" id="toggle2">Mostrar</button>
        <div id="confirmMsg"></div>
        <br><br>

        <button type="submit">Crear cuenta</button>
    </form>

    <p><a href="{{ url('/') }}">Volver al inicio</a></p>
@endsection

@section('script')
    <script>
        (() => {
            const form = document.getElementById('registerForm');
            const pass = document.getElementById('password');
            const pass2 = document.getElementById('password_confirmation');

            const passMsg = document.getElementById('passMsg');
            const confirmMsg = document.getElementById('confirmMsg');

            const toggle1 = document.getElementById('toggle1');
            const toggle2 = document.getElementById('toggle2');

            function toggleVisibility(input, btn) {
                input.type = (input.type === 'password') ? 'text' : 'password';
                btn.textContent = (input.type === 'password') ? 'Mostrar' : 'Ocultar';
            }

            toggle1.addEventListener('click', () => toggleVisibility(pass, toggle1));
            toggle2.addEventListener('click', () => toggleVisibility(pass2, toggle2));

            function validate() {
                passMsg.textContent = '';
                confirmMsg.textContent = '';

                let ok = true;

                if ((pass.value || '').length < 8) {
                    passMsg.textContent = 'La contraseña debe tener mínimo 8 caracteres.';
                    ok = false;
                }

                // Solo valida "no coinciden" si ya escribió confirmación
                if (pass2.value && pass.value !== pass2.value) {
                    confirmMsg.textContent = 'Las contraseñas no coinciden.';
                    ok = false;
                }

                return ok;
            }

            // opcional pero recomendado: validación en vivo
            pass.addEventListener('input', validate);
            pass2.addEventListener('input', validate);

            form.addEventListener('submit', (e) => {
                if (!validate()) e.preventDefault();
            });

            validate();
        })();
    </script>
@endsection
