@extends('session.index')

@section('login')

<form class="form w-100" novalidate="novalidate" id="kt_password_reset_form"
    data-kt-redirect-url="{{ route('login') }}" action="{{ route('email.verify') }}" method="POST">
    @csrf
    <!--begin::Heading-->
    <div class="text-center mb-10">
        <!--begin::Title-->
        <h1 class="text-gray-900 fw-bolder mb-3">Forgot Password ?</h1>
        <!--end::Title-->
        <!--begin::Link-->
        <div class="text-gray-500 fw-semibold fs-6">Enter your email to reset your password.
        </div>
        <!--end::Link-->
    </div>
    <!--begin::Heading-->
    <!--begin::Input group=-->
    <div class="fv-row mb-8">
        <!--begin::Email-->
        <input type="text" placeholder="Email" name="email" class="form-control bg-transparent" />
        <!--end::Email-->
    </div>
    <!--begin::Actions-->
    <div class="d-flex flex-wrap justify-content-center pb-lg-0">
        <button type="submit" id="kt_sign_in_submit" class="btn btn-primary me-4">
            <!--begin::Indicator label-->
            <span class="indicator-label">Verify</span>
            <!--end::Indicator label-->
            <!--begin::Indicator progress-->
            <span class="indicator-progress">Please wait...
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            <!--end::Indicator progress-->
        </button>
        <a href="{{ route('login') }}" class="btn btn-light">Cancel</a>
    </div>
    <!--end::Actions-->
</form>
@endsection
