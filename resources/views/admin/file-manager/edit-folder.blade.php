<div class="modal fade" id="kt_modal_edit_folder" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header" id="kt_modal_add_user_header">
                <!--begin::Modal title-->
                <h2 class="fw-bold">Rename Folder</h2>
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
                <form id="kt_modal_edit_folder_form" class="form" autocomplete="off" action="" enctype="multipart/form-data">
                    @csrf
                    <!--begin::Scroll-->
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" data-kt-scroll="true"
                         data-kt-scroll-activate="true" data-kt-scroll-max-height="auto" data-kt-scroll-offset="300px">
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">Folder Name</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" name="edit_folder" id="edit_folder"
                                   class="form-control form-control-solid mb-3 mb-lg-0" required />
                            <input type="hidden" id="folderId" name="folderId"> <!-- Changed to hidden input -->
                        </div>
                    </div>
                    <!--end::Scroll-->
                    <!--begin::Actions-->
                    <div class="text-center pt-10">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Discard</button>
                        <button type="submit" class="btn btn-primary" id="button_submit">
                            <span class="indicator-label">Save Changes</span> <!-- Changed text for clarity -->
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
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="{{ asset('assets/plugins/global/plugins.bundle.js')}}"></script>

<script>
    // Element to indecate
    var button = document.querySelector("#button_submit");

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
        const form = document.getElementById('kt_modal_edit_folder');
        const submitButton = document.getElementById('button_submit');

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
