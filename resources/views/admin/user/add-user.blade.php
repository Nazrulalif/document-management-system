<div class="modal fade" id="kt_modal_add_user" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header" id="kt_modal_add_user_header">
                <!--begin::Modal title-->
                <h2 class="fw-bold">Add User</h2>
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body px-5 my-7">
                <!--begin::Form-->
                <form id="kt_modal_add_user_form" class="form" action="{{ route('user.create') }}" method="post"
                    autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <!--begin::Scroll-->
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add_user_scroll"
                        data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto"
                        data-kt-scroll-dependencies="#kt_modal_add_user_header"
                        data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">Full Name</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" name="full_name" class="form-control form-control-solid mb-3 mb-lg-0"
                                placeholder="Full name" required />
                            <!--end::Input-->
                            @error('full_name')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">Email</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="email" name="email" class="form-control form-control-solid mb-3 mb-lg-0"
                                placeholder="Email" required />
                            <!--end::Input-->
                            @error('email')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">Company</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select class="form-select form-control form-select-solid mb-3 mb-lg-0" data-control="select2"
                             data-close-on-select="true"
                             data-allow-clear="true" multiple="multiple" name="org_name[]" data-placeholder="Select a company" required>
                                <option></option>
                                @foreach ($company as $company )
                                <option value="{{ $company->id }}">{{ $company->org_name }}</option>
                                @endforeach
                            </select>
                            <!--end::Input-->
                            @error('organization')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">Role</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select class="form-select form-control form-select-solid mb-3 mb-lg-0"
                                data-control="select2" name="role_name" data-placeholder="Select a role"
                                data-hide-search="true">
                                <option></option>
                                @foreach ($role as $role )
                                <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                @endforeach
                            </select>
                            <!--end::Input-->
                            @error('role')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-7">
                            <div class="col-md-6">
                                <!--begin::Label-->
                                <label class=" fw-semibold fs-6 mb-2">Ic Number/Passport Number</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="ic_number" class="form-control form-control-solid mb-3 mb-lg-0"
                                    placeholder="Ic Number/Passport Number"  />
                                <!--end::Input-->
                                @error('ic_number')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <!--begin::Label-->
                                <label class=" fw-semibold fs-6 mb-2">Nationality</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="nationality"
                                    class="form-control form-control-solid mb-3 mb-lg-0" placeholder="nationality"
                                     />
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
                                <label class=" fw-semibold fs-6 mb-2">Race</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="race" class="form-control form-control-solid mb-3 mb-lg-0"
                                    placeholder="race"  />
                                <!--end::Input-->
                                @error('race')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <!--begin::Label-->
                                <label class=" fw-semibold fs-6 mb-2">Gender</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select class="form-select form-control form-select-solid mb-3 mb-lg-0"
                                data-control="select2" name="gender" data-placeholder="Select a gender"
                                data-hide-search="true" >
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

                        <!--end::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class=" fw-semibold fs-6 mb-2">Position</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" name="position" class="form-control form-control-solid mb-3 mb-lg-0"
                                placeholder="position"  />
                            <!--end::Input-->
                            @error('position')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!--end::Scroll-->
                    <!--begin::Actions-->
                    <div class="text-center pt-10">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Discard</button>
                        <button type="submit" class="btn btn-primary" id="kt_button_submit">
                            <span class="indicator-label">Submit</span>
                            <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                    <!--end::Actions-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>

<script>
    // Element to indecate
    var button = document.querySelector("#kt_button_submit");

    // Handle button click event
    button.addEventListener("click", function () {
        // Activate indicator
        button.setAttribute("data-kt-indicator", "on");

        // Disable indicator after 3 seconds
        setTimeout(function () {
            button.removeAttribute("data-kt-indicator");
        }, 3000);
    });

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('kt_modal_add_user_form');
        const submitButton = document.getElementById('kt_button_submit');

        form.addEventListener('submit', function (event) {
            let isValid = true;

            // Loop through required fields
            form.querySelectorAll('input[required]').forEach(function (input) {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid'); // Add a Bootstrap invalid class
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                event.preventDefault(); // Prevent form submission if validation fails
                toastr.error('Please complete all required fields.');
            } else {
                submitButton.disabled = true; // Disable submit button to prevent multiple submissions
            }
        });
    });

</script>
