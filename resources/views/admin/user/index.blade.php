@extends('layouts.user_type.auth')

@section('content')

@include('admin.user.index-navbar')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid py-3 py-lg-8">
            <!--begin::Content container-->
        
            @yield('content-user')

            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->

</div>



@endsection
