<!doctype html>
<html lang="uk"> <!-- {{ str_replace('_', '-', app()->getLocale()) }}-->
<head>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="user-id" content="{{ Illuminate\Support\Facades\Auth::id() }}">

    <title>{{ __(config('app.name', 'CheckIt для Вчителів')) }}</title>
    <link rel="icon" href="{{ URL::asset('/favicon.ico') }}" type="image/x-icon"/>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @include('layouts.headers')
</head>
<body>
    <div id="app">
        @include('layouts.navbar')
        <main style="margin-top: 6em">
            @yield('content')
        </main>
    </div>
</body>
</html>
