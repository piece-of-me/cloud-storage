<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title>Storage</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}"/>

    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
    @vite('resources/src/assets/css/main.css')
</head>
<body>
@yield('content')
</body>
</html>
