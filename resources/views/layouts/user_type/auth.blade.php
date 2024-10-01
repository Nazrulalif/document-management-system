@extends('layouts.app')

@section('auth')

@if (!Request::is('admin/report/print'))
    @if(Auth::check() && Auth::user()->role_guid == 1 || Auth::user()->role_guid == 2 || Auth::user()->role_guid == 3)
        @include('layouts.navbars.auth.sidebar-admin')
    @else
        @include('layouts.navbars.auth.sidebar-user')
    @endif
@endif

<main class="main-content">
    @include('layouts.navbars.auth.nav')
    <div class="py-0">
        @yield('content')
        @include('layouts.footers.auth.footer')
    </div>
</main>


@endsection
