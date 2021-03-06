<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ URL::to('/') }}">
    <title>MSC</title>
    <link href="{{ asset('/css/app.css?v=' . $hash) }}" rel="stylesheet">
    @auth
        <script src="{{ asset('/js/app.js?v=' . $hash) }}" defer></script>
    @endauth
</head>
<body>
<div id="app">
    @yield('content')
</div>
</body>
</html>
