@extends('layouts.app')
@section('content')
    <section class="">
        <div class="container">
            <div class="mt-3 mb-3 bg-white rounded">
                <lessons-page
                    username="{{ Auth::user()->name }}"
                    is_premium_user="{{ Auth::user()->hasRole('premium') }}"></lessons-page>
            </div>
        </div>
    </section>

@endsection
