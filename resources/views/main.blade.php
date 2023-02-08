<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title>Storage</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}"/>
</head>
<body>
<div id="app"></div>
@vite('resources/src/main.js')
</body>
</html>
