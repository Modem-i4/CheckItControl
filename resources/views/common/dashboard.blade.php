@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <dashboard-page
                :is_premium="{{Auth::user()->hasRole("premium") ? "true" : "false"}}"></dashboard-page>
            </div>
        </div>
    </div>
@endsection
