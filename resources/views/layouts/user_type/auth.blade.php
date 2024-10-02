@extends('layouts.app')

@section('auth')

@if(Auth::check() && Auth::user()->role_guid == 1 || Auth::user()->role_guid == 2 || Auth::user()->role_guid == 3)
@include('layouts.navbars.auth.sidebar-admin')
@else
@include('layouts.navbars.auth.sidebar-user')
@endif

<main class="main-content">
    @if(Auth::check() && Auth::user()->role_guid == 1 || Auth::user()->role_guid == 2 || Auth::user()->role_guid == 3)
    @include('layouts.navbars.auth.nav-admin')
    @else
    @include('layouts.navbars.auth.nav-user')
    @endif

    <div class="py-0">
        @yield('content')
        @include('layouts.footers.auth.footer')
    </div>
</main>


@endsection
