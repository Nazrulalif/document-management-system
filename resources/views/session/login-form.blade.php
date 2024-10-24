@extends('session.index')

@section('login')
<form class="form w-100" id="kt_sign_in_form" action="{{ route('login.post') }}" method="post"
    enctype="multipart/form-data">

    @csrf
    <!--begin::Heading-->
    <div class="text-center mb-11">
        <!--begin::Title-->
        <h1 class="text-gray-900 fw-bolder mb-3">Sign In</h1>
        <!--end::Title-->
        <!--begin::Subtitle-->
        <div class="text-gray-500 fw-semibold fs-6">Document Management System</div>
        <!--end::Subtitle=-->
    </div>
    <!--begin::Heading-->
    @if ($isParentExist)
    <div class="row g-3 mb-9">
        <!--begin::Col-->
        <div class="col-md-12">
            <!--begin::Google link=-->
            <a href="{{ route('azure.redirect') }}"
                class="btn btn-flex btn-outline btn-text-gray-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100">
                <img alt="Logo" src="assets/media/svg/brand-logos/microsoft-5.svg" class="h-15px me-3">Microsoft Azure</a>
            <!--end::Google link=-->
        </div>
        <!--end::Col-->
    </div>
    <div class="separator separator-content my-14">
        <span class="w-200px text-gray-500 fw-semibold fs-7">Or with email</span>
    </div>
    <!--begin::Input group=-->
    <div class="fv-row mb-8">
        <!--begin::Email-->
        <input type="text" placeholder="Email" name="email" class="form-control bg-transparent" />
        <!--end::Email-->
    </div>
    <!--end::Input group=-->
    <div class="fv-row mb-3">
        <!--begin::Password-->
        <input type="password" placeholder="Password" name="password" autocomplete="off"
            class="form-control bg-transparent" />
        <!--end::Password-->
    </div>
    <!--end::Input group=-->
    <!--begin::Wrapper-->
    <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
        <div></div>
        <!--begin::Link-->
        <a href="{{ route('password.request') }}" class="link-primary">Forgot
            Password ?</a>
        <!--end::Link-->
    </div>
    <!--end::Wrapper-->
    <!--begin::Submit button-->
    <div class="d-grid mb-10">
        <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
            <!--begin::Indicator label-->
            <span class="indicator-label">Sign In</span>
            <!--end::Indicator label-->
            <!--begin::Indicator progress-->
            <span class="indicator-progress">Please wait...
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            <!--end::Indicator progress-->
        </button>
    </div>
    <!--end::Submit button-->
    @else
    <div class="row g-3 mb-9">
       
        <!--begin::Col-->
        <div class="col-md-12">
            <!--begin::Google link=-->
            <a href="{{ route('register.parent') }}"
                class="btn btn-flex btn-outline btn-text-gray-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100">
                <img alt="Logo" src="assets/media/svg/brand-logos/man_enter.svg" class="h-25px me-3">Sign Up Parent
                Company</a>
            <!--end::Google link=-->
        </div>
        <!--end::Col-->
        <div class="separator separator-content my-14">
            <span class="w-1000px text-gray-500 fw-semibold fs-7">Sign Up parent company to continue sign in</span>
        </div>
    </div>
    
    @endif


</form>
@endsection
