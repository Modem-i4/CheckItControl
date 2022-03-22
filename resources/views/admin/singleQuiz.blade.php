@extends('layouts.app')

@section('content')
    <section class="">
        <div class="container">
            <div class="mt-3 mb-3 bg-white rounded">
                <single-quiz
                    school_id="{{ rtrim($_SERVER['REQUEST_URI'], '/') }}"></single-quiz>
            </div>
        </div>
    </section>

@endsection
