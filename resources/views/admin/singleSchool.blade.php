@extends('layouts.app')
@section('content')
    <section class="">
        <div class="container">
            <div class="mt-3 mb-3 bg-white rounded">
                <single-school
                    school_id="{{ Auth::user()->school_id }}"></single-school>
            </div>
        </div>
    </section>

@endsection
