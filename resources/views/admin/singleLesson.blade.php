@extends('layouts.app')

@push('extra-scripts')
    <script type="text/javascript" src="/js/Photon/3rdparty/swfobject.js"></script>
    <script type="text/javascript" src="/js/Photon/3rdparty/web_socket.js"></script>
    <script type="text/javascript" src="/js/Photon/Photon-Javascript_SDK.min.js"></script>
@endpush

@section('content')
    <section class="">
        <div class="container">
            <div class="mt-3 mb-3 bg-white rounded">
                <single-lesson-page :id="{{$id}}"></single-lesson-page>
            </div>
        </div>
    </section>

@endsection
