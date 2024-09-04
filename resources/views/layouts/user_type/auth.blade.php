@extends('layouts.app')

@section('auth')

@if(Auth::check() && Auth::user()->role_guid == 1)
    @include('layouts.navbars.auth.sidebar-admin')
@else
    @include('layouts.navbars.auth.sidebar-user')
@endif

<main class="main-content">
    @include('layouts.navbars.auth.nav')
    <div class="py-0">
        @yield('content')
    @include('layouts.footers.auth.footer')
    </div>
</main>

@endsection
