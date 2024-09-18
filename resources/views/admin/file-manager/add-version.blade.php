<div class="modal fade" id="kt_modal_add_version" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header" id="kt_modal_add_user_header">
                <!--begin::Modal title-->
                <h2 class="fw-bold">Add New Version</h2>
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
                <form id="kt_modal_edit_file_form" class="form" method="post" autocomplete="off"
                    enctype="multipart/form-data">
                    @csrf
                    <!--begin::Scroll-->
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" data-kt-scroll="true"
                        data-kt-scroll-activate="true" data-kt-scroll-max-height="auto" data-kt-scroll-offset="300px">
                        <input type="hidden" value="{{ $data->uuid }}" id="file_id" required>
                        <input type="hidden" value="{{ $data->doc_type }}" id="file_type" required>
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">Change Title</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" name="change_title" id="change_title"
                                class="form-control form-control-solid mb-3 mb-lg-0" required />
                            <!--end::Input-->
                            @error('change_title')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">Description</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <textarea class="form-control form-control form-control-solid" data-kt-autosize="true"
                                id="change_description" name="change_description" required></textarea>
                            <!--end::Input-->
                            @error('change_description')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">Upload file</label>
                            <!--end::Label-->
                            <!-- Progress bar -->
                            <div class="progress mt-3" style="display: none">
                                <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%"
                                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                            </div>
                            <!-- Status text -->
                            <div id="status-text" class="mt-2"></div>

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
                    <!--end::Scroll-->
                    <!--begin::Actions-->
                    <div class="text-center pt-10">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Discard</button>
                        <button type="button" class="btn btn-primary" id="button_submit">
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
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="{{ asset('assets/plugins/global/plugins.bundle.js')}}"></script>
<script>
    Dropzone.autoDiscover = false; // Disable auto-discover of dropzones

    // Function to perform OCR on image files (reused from Code 2)
    function startOCR(file, callback) {
        const corePath = window.navigator.userAgent.indexOf("Edge") > -1 ?
            '{{ asset('assets/js/tesseract-core.asm.js') }}': '{{ asset('assets/js/tesseract-core.wasm.js') }}';

        const worker = new Tesseract.TesseractWorker({
            corePath: corePath,
        });

        $('.progress').show();
        $('#status-text').text('Processing...');

        worker.recognize(file, 'eng') // Perform OCR with English language
            .progress(function (packet) {
                if (packet.status === 'recognizing text') {
                    var progress = Math.round(packet.progress * 100);
                    $('#progress-bar').css('width', progress + '%').attr('aria-valuenow', progress).text(progress +
                        '%');
                    $('#status-text').text('Recognizing text: ' + progress + '%');
                }
            })
            .then(function (result) {
                $('#status-text').text('OCR Complete! Uploading...');
                callback(result.text); // Pass the OCR text to the callback
            })
            .catch(function (error) {
                console.error('OCR error:', error);
                $('#status-text').text('An error occurred during OCR.');
                callback(null); // No text extracted if an error occurs
            });
    }

    // Initialize Dropzone
    var myDropzone = new Dropzone("#kt_dropzonejs_example_1", {
        autoProcessQueue: false, // Prevent automatic file upload
        url: "/admin/add-version", // Laravel route for handling file upload
        paramName: "file", // The name that will be used to transfer the file
        maxFiles: 1, // Allow only one file
        maxFilesize: 10, // Maximum file size in MB
        addRemoveLinks: true, // Show remove link
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}" // CSRF token for Laravel
        },
        init: function () {
            var submitButton = document.querySelector("#button_submit");
            var myDropzone = this; // Closure

            // Add additional form data to the file upload request
            myDropzone.on("sending", function (file, xhr, formData) {
                var change_title = document.querySelector('#change_title').value;
                var change_description = document.querySelector('#change_description').value;
                var fileID = document.querySelector('#file_id').value;

                formData.append("change_title", change_title);
                formData.append("change_description", change_description);
                formData.append("id", fileID);

                // Append OCR content if available
                if (file.ocrText) {
                    formData.append("ocr_content", file.ocrText);
                }
            });

            // On submit button click, process the Dropzone file upload
            submitButton.addEventListener("click", function () {
                submitButton.setAttribute("data-kt-indicator", "on");
                submitButton.disabled = true;

                // Check if a file is added to the dropzone
                if (myDropzone.getQueuedFiles().length > 0) {
                    var file = myDropzone.getQueuedFiles()[0];
                    var fileType = document.querySelector('#file_type').value;

                    var allowedTypes = {
                        'images': ['image/jpeg', 'image/png', 'image/gif'],
                        'pdf': ['application/pdf'],
                        'xlsx': [
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        ],
                        'csv': ['text/csv'],
                        'docx': ['application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                        ],
                        'doc': ['application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                        ],
                        'pptx': [
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
                        ]
                    };

                    function isFileTypeAllowed(file, type) {
                        return allowedTypes[type] && allowedTypes[type].includes(file.type);
                    }

                    if (!isFileTypeAllowed(file, fileType)) {
                        Swal.fire({
                            text: "Please upload a valid file of type: " + fileType + ".",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });

                        submitButton.removeAttribute("data-kt-indicator");
                        submitButton.disabled = false;
                        myDropzone.removeAllFiles(true);
                    } else {
                        // Perform OCR if the file is an image
                        if (['image/jpeg', 'image/png'].includes(file.type)) {
                            startOCR(file, function (ocrText) {
                                file.ocrText = ocrText; // Attach OCR result to the file
                                myDropzone.processQueue(); // Proceed with the upload
                            });
                        } else {
                            myDropzone.processQueue(); // Directly process non-image files
                        }
                    }
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
                    submitButton.disabled = false;
                }
            });

            // Success event handler
            myDropzone.on("success", function (file, response) {

                if (response.success == false) {

                    Swal.fire({
                        text: response.error,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                } else {
                    Swal.fire({
                        text: "File uploaded successfully!",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    }).then(function () {

                        const editModal = document.getElementById('kt_modal_add_version');
                        if (editModal) {
                            const modalInstance = bootstrap.Modal.getInstance(editModal);
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                        }

                        if (response.uuid) {
                            window.location.href = "/admin/file-details/" + response.uuid;
                        } else {
                            window.location.reload();
                        }

                        myDropzone.removeAllFiles(true);
                        submitButton.removeAttribute("data-kt-indicator");
                        submitButton.disabled = false;
                    });
                }

            });

            // Error event handler
            myDropzone.on("error", function (file, response) {
                toastr.error(response.message || 'Error uploading file.', 'Uploading Error', {
                    closeButton: true,
                    progressBar: true,
                    timeOut: 5000,
                    positionClass: 'toastr-top-right',
                });

                Swal.fire({
                    text: "There was an error uploading the file. Please try again.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });

                submitButton.removeAttribute("data-kt-indicator");
                submitButton.disabled = false;
            });
        }
    });

</script>

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
        const form = document.getElementById('kt_modal_add_version');
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
