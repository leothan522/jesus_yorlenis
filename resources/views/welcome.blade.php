@extends('layouts.bootstrap')

@section('content')
    <div class="text-center pt-1 mb-5 pb-1">
        @auth
            <a class="text-muted" href="{{ route('profile.show') }}">{{ __('Profile') }}</a>
            <a class="text-muted ms-3" href="{{ url('/dashboard') }}">Dashboard</a>
        @else
            <a class="text-muted" href="{{ route('login') }}">{{ __('Log in') }}</a>
            @if (Route::has('register'))
                <a class="text-muted ms-3" href="{{ route('register') }}">{{ __('Register') }}</a>
            @endif
        @endauth
    </div>
@endsection
