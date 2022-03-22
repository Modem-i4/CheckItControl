@extends('layouts.app')

@section('content')
    <section class="">
        <div class="container">
            <div class="mt-3 mb-3 bg-white rounded">
                <quizzes-page
                user_id="{{Auth::id()}}"
                ></quizzes-page>
            </div>
        </div>
    </section>

@endsection
