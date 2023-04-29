<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> 
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- Anti pattern but it works for now -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-geosearch@2.6.0/assets/css/leaflet.css">
</head>

<body>

</body>

    <div id="app">
        <router-view></router-view>
    </div>

</html>