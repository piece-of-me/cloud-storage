<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8"/>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Vite + Vue</title>

    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
    @vite('resources/css/app.css')
</head>
<body>

@yield('content')

</body>
</html>
