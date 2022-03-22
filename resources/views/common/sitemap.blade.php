<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="user-id" content="{{ Illuminate\Support\Facades\Auth::id() }}">

    <title>{{ config('app.name', 'CheckIt для Вчителів') }}</title>
    <link rel="icon" href="{{ URL::asset('/favicon.ico') }}" type="image/x-icon"/>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
    .links a {
        font-size:xx-large;
        font-weight:bold;
    }
    h1 {
        font-weight:bold;
    }
    iframe {
        height: 450px;
    }
    header img {
        height: 70px;
    }
    .wrapper {
        margin-bottom: 70px;
    }
    </style>
    @include('layouts.headers')
</head>
<body>
    <header>
        <img src="img/checkit-logo.png" class="d-block mx-auto">
    </header>
    <div class="container-fluid row wrapper">
            <div class="col-md-8">
                <div class="container">
                    <h1 class="">Протестуй свої знання вже сьогодні!</h1>
                    <h3 class="">Гра Чекіт дозволить вашим учням краще засвоїти матеріал, а Вам -- краще зафіксувати їх досягнення!</h3>
                    <h4 class="">Пропонуємо Вам відео до ознайомлення:</h3>
                </div>
                <iframe class="w-100" src="https://www.youtube.com/embed/h-_dES3yZc0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
            <div class="col-md-4 links">
                <a href="/public/game" class="btn btn-outline-success btn-block mb-5">Грати</a>
                <a href="/admin" class="btn btn-outline-success btn-block mb-5">Для вчителів</a>
                <a href="/files/CheckIt.apk" class="btn btn-outline-success btn-block mb-5" download>Завантажити на андроїд</a>
            </div>
    </div>
    <footer class="bg-light text-center text-lg-start fixed-bottom b-0 w-100">
  <div class="text-center p-3" style="background-color: rgba(155, 189, 221, 0.2);color:#488EA5;">
    © 2021 Copyright: EtCetera спільно з Unicef
  </div>
</body>
