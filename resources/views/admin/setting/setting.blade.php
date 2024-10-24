@extends('layouts.user_type.auth')

@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid py-3 py-lg-8">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-fluid">
                <div class="card mb-5 mb-xl-10">
                    <div class="card-header">
                        <!--begin::Navs-->
                        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                            <!--begin::Nav item-->
                            <li class="nav-item ">
                                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ (Request::is('admin/system-setting',) ? 'active' : '') }}"
                                    href="{{ route('setting.index') }}">Main</a>
                            </li>
                        </ul>
                        <!--begin::Navs-->
                    </div>
                    <div class="card-body p-9">
                        @yield('setting_content')
                    </div>
                </div>

            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->

</div>


@endsection
