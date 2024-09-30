@extends('layouts.user_type.guest')

@section('content')

<!--begin::Authentication - Sign-in -->
<div class="d-flex flex-column flex-root" id="kt_app_root">
    <!--begin::Page bg image-->
    <style>
        body {
            background-image: url( {{ asset('assets/media/auth/bg3.jpg') }});
        }

        [data-bs-theme="dark"] body {
            background-image: url({{ asset('assets/media/auth/bg3-dark.jpg') }});
        }

    </style>
    <!--end::Page bg image-->
    <!--begin::Authentication - Sign-in -->
    <div class="d-flex flex-column flex-column-fluid flex-lg-row">
        <!--begin::Aside-->
        <div class="d-flex flex-center w-lg-50 pt-15 pt-lg-0 px-10">
            <!--begin::Aside-->
            <div class="d-flex flex-center flex-lg-start flex-column">
                <!--begin::Logo-->
                <a href="index.html" class="mb-7">
                    <img alt="Logo" src="{{ asset('assets/media/logos/custom-3.svg') }}" />
                </a>
                <!--end::Logo-->
                <!--begin::Title-->
                <h2 class="text-white fw-normal m-0">Branding tools designed for your business</h2>
                <!--end::Title-->
            </div>
            <!--begin::Aside-->
        </div>
        <!--begin::Aside-->
        <!--begin::Body-->
        <div
            class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12 p-lg-20">
            <!--begin::Card-->
            <div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-600px p-20">
                <!--begin::Wrapper-->
                <div class="d-flex flex-center flex-column flex-column-fluid px-lg-10 pb-15 pb-lg-0">
                    <!--begin::Form-->

                    @yield('login')
                   
                    <!--end::Form-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Body-->
    </div>
    <!--end::Authentication - Sign-in-->
</div>
<!--end::Authentication - Sign-in-->

<script>
    // Element to indecate
var button = document.querySelector("#kt_sign_in_submit");

// Handle button click event
button.addEventListener("click", function() {
    // Activate indicator
    button.setAttribute("data-kt-indicator", "on");

    // Disable indicator after 3 seconds
    setTimeout(function() {
        button.removeAttribute("data-kt-indicator");
    }, 3000);
});
</script>
@endsection
