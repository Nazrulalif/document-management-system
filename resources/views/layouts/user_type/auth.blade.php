@extends('layouts.app')

@section('auth')

@if (!Request::is('admin/report/print'))
    @if(Auth::check() && Auth::user()->role_guid == 1)
        @include('layouts.navbars.auth.sidebar-admin')
    @else
        @include('layouts.navbars.auth.sidebar-user')
    @endif
@endif

@if (!Request::is('admin/report/print'))
<main class="main-content">
    @include('layouts.navbars.auth.nav')
    <div class="py-0">
        @yield('content')
        @include('layouts.footers.auth.footer')
    </div>
</main>
@else
<main class="main-content">
    <div class="py-0">
        @yield('content')
    </div>
</main>
@endif

@endsection
