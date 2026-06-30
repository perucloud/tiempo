<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | TIEMPO Delivery</title>
    <style>
        body { margin: 0; min-height: 100vh; font-family: Arial, sans-serif; background: #f6f7fb; color: #111827; }
        header { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 18px 24px; background: #fff; border-bottom: 1px solid #e5e7eb; }
        main { padding: 24px; }
        h1 { margin: 0 0 6px; font-size: 26px; }
        p { margin: 0; color: #6b7280; }
        form { margin: 0; }
        button { border: 0; border-radius: 6px; padding: 10px 14px; background: #111827; color: #fff; font-weight: 700; cursor: pointer; }
        .panel { max-width: 960px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 22px; }
    </style>
</head>
<body>
    <header>
        <div>
            <strong>TIEMPO Delivery</strong>
            <p>{{ auth()->user()->name }} | {{ auth()->user()->roleLabel() }}</p>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit">Cerrar sesion</button>
        </form>
    </header>

    <main>
        <section class="panel">
            <h1>Dashboard administrativo</h1>
            <p>Base protegida para la operacion de TIEMPO. Los modulos se activaran por fase.</p>
        </section>
    </main>
</body>
</html>
