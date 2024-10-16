@extends('session.index')

@section('login')
<div class="text-center mb-11">
    <!--begin::Title-->
    <h1 class="text-gray-900 fw-bolder mb-3">🎉 Account Registration Successful!</h1>
    <!--end::Title-->
    
    <!--begin::Subtitle-->
    <div class="text-gray-500 fw-semibold fs-6 mb-4">
        Thank you for registering! A confirmation email has been sent to your inbox. Please check your email to set your password.
    </div>
    <!--end::Subtitle-->

    <!--begin::Button-->
    <a href="{{ route('login') }}" class="btn btn-primary fw-semibold fs-5">
        Go to Login Page
    </a>
    <!--end::Button-->
</div>

@endsection