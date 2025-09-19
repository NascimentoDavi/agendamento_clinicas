<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Agendamentos Clínica') - Clínica Psicologia FAESA</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- FAVICON -->
    <link rel="icon" type="image/png" href="/favicon_faesa.png">

    @vite(['resources/css/app.css'])

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- SEÇÃO DE CSS ESPECÍFICO -->
    @yield('styles')
</head>
<body class="bg-body-secondary">

    @include('components.navbar')

    <main class="mx-2">
        @yield('content')
    </main>

    <!-- JS GERAL -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SEÇÃO DE SCRIPTS ESPECÍFICOS -->
    @yield('scripts')

</body>
</html>
