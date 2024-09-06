<div class="modal fade" id="kt_modal_import_users" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Import Users</h2>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <div class="modal-body px-5 my-7">

                <form id="kt_modal_export_users_form" class="form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add_user_scroll"
                        data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto"
                        data-kt-scroll-dependencies="#kt_modal_add_user_header"
                        data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold form-label mb-2">Download template Excel here</label><br>
                            <a href="#" class="btn btn-secondary" style="display: flow-root">
                                <i class="ki-duotone ki-fasten fs-2x"><span class="path1"></span><span
                                        class="path2"></span></i>
                                <span class="fw-bold">Excel Template</span>
                            </a>
                        </div>
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">Company</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select class="form-select form-control form-select-solid mb-3 mb-lg-0" data-selection="org_name"
                                data-control="select2" name="org_name" id="org_name" data-placeholder="Select a company" required>
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
                        <div class="fv-row mb-10">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">Role</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select class="form-select form-control form-select-solid mb-3 mb-lg-0" data-selection="role_name"
                                data-control="select2" name="role_name" id="role_name" data-placeholder="Select a role"
                                data-hide-search="true" required>
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
                        <div class="fv-row mb-10">
                            <label class="required fs-6 fw-semibold form-label mb-2">Upload file here</label>
                            <div class="dropzone" id="kt_dropzonejs_example_1">
                                <div class="dz-message needsclick">
                                    <i class="ki-duotone ki-file-up fs-3x text-primary">
                                        <span class="path1"></span><span class="path2"></span>
                                    </i>
                                    <div class="ms-4">
                                        <h3 class="fs-5 fw-bold text-gray-900 mb-1">Drop files here or click to upload.
                                        </h3>
                                        <span class="fs-7 fw-semibold text-gray-500">Upload up to 1 file only</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="text-center pt-10">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Discard</button>
                        <button type="button" class="btn btn-primary" id="button_submit">
                            <span class="indicator-label">Submit</span>
                            <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/plugins/global/plugins.bundle.js')}}"></script>
<script>
Dropzone.autoDiscover = false; // Disable auto-discover of dropzones

// Initialize Dropzone
var myDropzone = new Dropzone("#kt_dropzonejs_example_1", {
    autoProcessQueue: false, // Prevent automatic file upload
    url: "/admin/import-user", // Laravel route for handling file upload
    paramName: "file", // The name that will be used to transfer the file
    maxFiles: 1, // Allow only one file
    maxFilesize: 10, // Maximum file size in MB
    acceptedFiles: ".xlsx,.xls,.csv", // Accept only Excel and CSV files
    addRemoveLinks: true, // Show remove link
    headers: {
        'X-CSRF-TOKEN': "{{ csrf_token() }}" // CSRF token for Laravel
    },
    init: function () {
        var submitButton = document.querySelector("#button_submit");
        var myDropzone = this; // Closure

        // Add additional form data to the file upload request
        myDropzone.on("sending", function(file, xhr, formData) {
             // Use querySelector to get selected values
             var company = document.querySelector('select[data-selection="org_name"]').value;
            var role = document.querySelector('select[data-selection="role_name"]').value;
            // Check if values are correctly retrieved
            if (!company || !role) {
                return; // Stop further processing
            }

            // Append form inputs to the request
            formData.append("org_name", company);
            formData.append("role_name", role);
        });

        // On submit button click, process the Dropzone file upload
        submitButton.addEventListener("click", function () {
            // Activate the spinner (button indicator)
            submitButton.setAttribute("data-kt-indicator", "on");
            submitButton.disabled = true; // Disable the button to prevent multiple clicks

            // Check if a file is added to the dropzone
            if (myDropzone.getQueuedFiles().length > 0) {
                myDropzone.processQueue(); // Process the file upload
            } else {
                Swal.fire({
                    text: "Please upload a file before submitting",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });
                submitButton.removeAttribute("data-kt-indicator");
                submitButton.disabled = false; // Re-enable the button after error
            }
        });

        // Success event handler
        myDropzone.on("success", function (file, response) {
            Swal.fire({
                text: "User details updated successfully!",
                icon: "success",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn fw-bold btn-primary",
                }
            }).then(function () {
                // Hide the modal
                const editModal = document.getElementById('kt_modal_import_users');
                if (editModal) {
                    const modalInstance = bootstrap.Modal.getInstance(editModal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                }

                // Optionally refresh the page or update the relevant parts of the UI
                window.location.reload();

                myDropzone.removeAllFiles(true); // Remove files from Dropzone

                submitButton.removeAttribute("data-kt-indicator");
                submitButton.disabled = false; // Re-enable the button
            });
        });

        // Error event handler
        myDropzone.on("error", function (file, response) {
            Swal.fire({
                text: "There was an error importing the file. Please try again.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn fw-bold btn-primary",
                }
            });

            submitButton.removeAttribute("data-kt-indicator");
            submitButton.disabled = false; // Re-enable the button after error
        });
    }
});


</script>
