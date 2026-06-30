<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | TIEMPO Delivery</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; font-family: Arial, sans-serif; background: #f6f7fb; color: #111827; }
        main { width: min(92vw, 380px); background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 28px; box-shadow: 0 16px 40px rgba(15, 23, 42, .08); }
        h1 { margin: 0 0 6px; font-size: 24px; }
        p { margin: 0 0 22px; color: #6b7280; }
        label { display: block; margin: 14px 0 6px; font-weight: 700; font-size: 14px; }
        input[type="email"], input[type="password"] { box-sizing: border-box; width: 100%; padding: 12px 13px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 15px; }
        .row { display: flex; align-items: center; gap: 8px; margin: 14px 0 18px; color: #374151; }
        button { width: 100%; border: 0; border-radius: 6px; padding: 12px 14px; background: #111827; color: #fff; font-weight: 700; cursor: pointer; }
        .error { margin: 12px 0 0; color: #b91c1c; font-size: 14px; }
    </style>
</head>
<body>
    <main>
        <h1>TIEMPO Admin</h1>
        <p>Acceso para operadores y administradores.</p>

        <form method="POST" action="{{ route('admin.login.store') }}">
            @csrf

            <label for="email">Correo</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>

            <label for="password">Contrasena</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>

            <label class="row">
                <input name="remember" type="checkbox" value="1">
                Recordar sesion
            </label>

            <button type="submit">Ingresar</button>

            @if ($errors->any())
                <div class="error">{{ $errors->first() }}</div>
            @endif
        </form>
    </main>
</body>
</html>
