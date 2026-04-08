@extends('layouts.subscriber') {{-- Your main layout file --}}

@section('content')
    <div class="db-main-content">
        <div class="container-fluid">
            <div class="account-wrapper">
                <div class="row">
                    <div class="col-12">
                        <div class="auth-box py-5 px-4  bg-dark rounded">
                            <div class="sec-content">
                            <div class="billing-wrapper text-white mb-4">
                                {{ $msg }}
                            </div>
                            <a href="{{ url('/') }}" class="btn btn-lg" style="background-color: #FFD700; color: white; border: none;">Go to Home</a>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection