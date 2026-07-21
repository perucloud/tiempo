<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Repartidor | TIEMPO Delivery</title>
    <link rel="stylesheet" href="{{ asset('css/courier.css') }}">
</head>
<body>
<main class="courier-login">
    <h1>TIEMPO Repartidor</h1>
    <p>Ingresa para iniciar o continuar tu turno.</p>
    <form method="POST" action="{{ route('courier.login.store') }}">
        @csrf
        <label for="email">Correo</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
        <label for="password">Contraseña</label>
        <input id="password" name="password" type="password" required>
        <button type="submit">Ingresar</button>
        @if($errors->any())<p role="alert">{{ $errors->first() }}</p>@endif
    </form>
</main>
</body>
</html>
