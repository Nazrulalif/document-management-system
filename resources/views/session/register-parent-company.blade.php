@extends('session.index')

@section('login')
<!--begin::Heading-->
<div class="text-center mb-11">
    <!--begin::Title-->
    <h1 class="text-gray-900 fw-bolder mb-3">Sign Up</h1>
    <!--end::Title-->
    <!--begin::Subtitle-->
    <div class="text-gray-500 fw-semibold fs-6">Document Management System</div>
    <!--end::Subtitle=-->
</div>
<!--begin::Stepper-->
<div class="stepper stepper-pills" id="kt_stepper_example_basic">
    <!--begin::Nav-->
    <div class="stepper-nav flex-center flex-wrap mb-10">
        <!--begin::Step 1-->
        <div class="stepper-item mx-8 my-4 current" data-kt-stepper-element="nav">
            <!--begin::Wrapper-->
            <div class="stepper-wrapper d-flex align-items-center">
                <!--begin::Icon-->
                <div class="stepper-icon w-40px h-40px">
                    <i class="stepper-check fas fa-check"></i>
                    <span class="stepper-number">1</span>
                </div>
                <!--end::Icon-->

                <!--begin::Label-->
                <div class="stepper-label">
                    <h3 class="stepper-title">
                        Company
                    </h3>

                    <div class="stepper-desc">
                        Register Company
                    </div>
                </div>
                <!--end::Label-->
            </div>
            <!--end::Wrapper-->

            <!--begin::Line-->
            <div class="stepper-line h-40px"></div>
            <!--end::Line-->
        </div>
        <!--end::Step 1-->

        <!--begin::Step 2-->
        <div class="stepper-item mx-8 my-4" data-kt-stepper-element="nav">
            <!--begin::Wrapper-->
            <div class="stepper-wrapper d-flex align-items-center">
                <!--begin::Icon-->
                <div class="stepper-icon w-40px h-40px">
                    <i class="stepper-check fas fa-check"></i>
                    <span class="stepper-number">2</span>
                </div>
                <!--begin::Icon-->

                <!--begin::Label-->
                <div class="stepper-label">
                    <h3 class="stepper-title">
                        Super Admin
                    </h3>

                    <div class="stepper-desc">
                        Register Super Admin
                    </div>
                </div>
                <!--end::Label-->
            </div>
            <!--end::Wrapper-->

            <!--begin::Line-->
            <div class="stepper-line h-40px"></div>
            <!--end::Line-->
        </div>
        <!--end::Step 2-->
    </div>
    <!--end::Nav-->

    <!--begin::Form-->
    <form class="form w-lg-500px mx-auto" action="{{ route('register.parent.post') }}" method="POST"
        enctype="multipart/form-data" id="kt_stepper_example_basic_form" autocomplete="off">
        @csrf
        <!--begin::Group-->
        <div class="mb-5">
            <!--begin::Step 1-->
            <div class="flex-column current" data-kt-stepper-element="content">
                <!--begin::Input group-->
                <div class="fv-row mb-7">
                    <!--begin::Label-->
                    <label class="form-label required">Company Name</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input type="text" class="form-control form-control-solid" name="org_name" placeholder=""
                        value="" />
                    <!--end::Input-->
                    @error('org_name')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <!--end::Input group-->
                <div class="row mb-7">
                    <div class="col-md-6">
                        <!--begin::Label-->
                        <label class="required fw-semibold fs-6 mb-2">Register Date</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="date" name="reg_date" class="form-control form-control-solid mb-3 mb-lg-0"
                            placeholder="" required />
                        <!--end::Input-->
                        @error('reg_date')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <!--begin::Label-->
                        <label class="required fw-semibold fs-6 mb-2">Register Number</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="text" name="org_number" class="form-control form-control-solid mb-3 mb-lg-0"
                            required />
                        <!--end::Input-->
                        @error('org_number')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="fv-row mb-7">
                    <!--begin::Label-->
                    <label class="required fw-semibold fs-6 mb-2">Address</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input type="text" name="org_address" class="form-control form-control-solid mb-3 mb-lg-0"
                        required />
                    <!--end::Input-->
                    @error('org_address')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <!--end::Input group-->
                <div class="row mb-7">
                    <div class="col-md-6">
                        <!--begin::Label-->
                        <label class="required fw-semibold fs-6 mb-2">State</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="text" name="org_place" class="form-control form-control-solid mb-3 mb-lg-0"
                            required />
                        <!--end::Input-->
                        @error('org_place')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <!--begin::Label-->
                        <label class="required fw-semibold fs-6 mb-2">Nature of Business</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="text" name="nature_of_business"
                            class="form-control form-control-solid mb-3 mb-lg-0" required />
                        <!--end::Input-->
                        @error('nature_of_business')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <!--begin::Step 1-->

            <!--begin::Step 1-->
            <div class="flex-column" data-kt-stepper-element="content">
                <!--begin::Input group-->
                <div class="fv-row mb-7">
                    <!--begin::Label-->
                    <label class="required fw-semibold fs-6 mb-2">Full Name</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input type="text" name="full_name" class="form-control form-control-solid mb-3 mb-lg-0" required />
                    <!--end::Input-->
                    @error('full_name')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <!--end::Input group-->

                <div class="fv-row mb-7">
                    <!--begin::Label-->
                    <label class="required fw-semibold fs-6 mb-2">Email</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input type="email" name="email" class="form-control form-control-solid mb-3 mb-lg-0" required />
                    <!--end::Input-->
                    @error('email')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-7">
                    <div class="col-md-6">
                        <!--begin::Label-->
                        <label class="required fw-semibold fs-6 mb-2">Ic Number/Passport Number</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="text" name="ic_number" class="form-control form-control-solid mb-3 mb-lg-0"
                            required />
                        <!--end::Input-->
                        @error('ic_number')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <!--begin::Label-->
                        <label class="required fw-semibold fs-6 mb-2">Nationality</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="text" name="nationality" class="form-control form-control-solid mb-3 mb-lg-0" />
                        <!--end::Input-->
                        @error('nationality')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!--end::Input group-->
                <div class="row mb-7">
                    <div class="col-md-6">
                        <!--begin::Label-->
                        <label class="required fw-semibold fs-6 mb-2">Race</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="text" name="race" class="form-control form-control-solid mb-3 mb-lg-0" required />
                        <!--end::Input-->
                        @error('race')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <!--begin::Label-->
                        <label class="required fw-semibold fs-6 mb-2">Gender</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select class="form-select form-control form-select-solid mb-3 mb-lg-0" data-control="select2"
                            name="gender" data-placeholder="Select a gender" data-hide-search="true" required>
                            <option></option>
                            <option value="male">male</option>
                            <option value="female">female</option>
                        </select>
                        <!--end::Input-->
                        @error('gender')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="fv-row mb-7">
                    <div class="col-md-12">
                        <!--begin::Label-->
                        <label class="required fw-semibold fs-6 mb-2">Position</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="text" name="position" class="form-control form-control-solid mb-3 mb-lg-0"
                            required />
                        <!--end::Input-->
                        @error('position')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <!--begin::Step 1-->
        </div>
        <!--end::Group-->

        <!--begin::Actions-->
        <div class="d-flex flex-stack">
            <!--begin::Wrapper-->
            <div class="me-2">
                <button type="button" class="btn btn-light btn-active-light-primary" data-kt-stepper-action="previous">
                    Back
                </button>
            </div>
            <!--end::Wrapper-->

            <!--begin::Wrapper-->
            <div>
                <button type="submit" class="btn btn-primary" data-kt-stepper-action="submit" id="kt_sign_in_submit">
                    <span class="indicator-label">
                        Sign Up
                    </span>
                    <span class="indicator-progress">
                        Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>

                <button type="button" class="btn btn-primary" data-kt-stepper-action="next">
                    Continue
                </button>
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Actions-->
    </form>
    <!--end::Form-->
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stepper = new KTStepper(document.getElementById('kt_stepper_example_basic'));

        stepper.on('kt.stepper.next', function (stepper) {
            stepper.goNext(); // Move to the next step
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        stepper.on('kt.stepper.previous', function (stepper) {
            stepper.goPrevious(); // Move to the previous step
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });

</script>

@endsection
