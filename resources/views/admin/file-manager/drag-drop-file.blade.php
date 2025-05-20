<?php
use Anhskohbo\NoCaptcha\Facades\NoCaptcha;
?>
<div class="modal fade" id="kt_modal_drag_drop" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Form-->
            <form id="kt_modal_edit_file_form" class="form" method="post" autocomplete="off"
                enctype="multipart/form-data">
                @csrf
                <!--begin::Modal header-->
                <div class="modal-header">
                    <!--begin::Modal title-->
                    <h2 class="fw-bold">Upload files</h2>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <!--end::Close-->
                </div>
                <!--end::Modal header-->
                <!--begin::Modal body-->
                <div class="modal-body pt-10 pb-15 px-lg-17">
                    <!--begin::Input group-->
                    <div class="form-group">
                        <!--begin::Form-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            @if (Auth::user()->role_guid == 1)

                                <label class="required fw-semibold fs-6 mb-2">Share to</label>
                                <select class="form-select form-select-solid" id="org_select_file"
                                    data-control="select2" data-placeholder="Select company..." name="org_name_file"
                                    required>
                                    <option></option>
                                    @foreach ($company as $item)
                                        <option value="{{ $item->id }}">{{ $item->org_name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <label class="required fw-semibold fs-6 mb-2">Share to</label>
                                <select class="form-select form-select-solid" id="org_select_file"
                                    data-control="select2" data-placeholder="Select company..." name="org_name_file"
                                    required>
                                    <option></option>
                                    @foreach ($user_orgs as $item)
                                        <option value="{{ $item->id }}">{{ $item->org_name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="fv-row">
                            <!-- Progress bar -->
                            <div class="progress mt-3" style="display: none">
                                <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%"
                                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                            </div>
                            <!-- Status text -->
                            <div id="status-text" class="mt-2"></div>
                            <!--begin::Dropzone-->
                            <div class="dropzone" id="kt_dropzonejs_example_1">
                                <!--begin::Message-->
                                <div class="dz-message needsclick">
                                    <i class="ki-duotone ki-file-up fs-3x text-primary"><span
                                            class="path1"></span><span class="path2"></span></i>

                                    <!--begin::Info-->
                                    <div class="ms-4">
                                        <h3 class="fs-5 fw-bold text-gray-900 mb-1">Drop files here or click to
                                            upload.</h3>
                                        <span class="fs-7 fw-semibold text-gray-500">Upload up to 10 files.</span> <br>
                                        <span class="fs-7 fw-semibold text-gray-500">Max file size is 100MB per
                                            file.</span> <br>
                                        <span class="fs-7 fw-semibold text-gray-500">Supported formats: jpg, jpeg, png, pdf, doc, docx, pptx, xlsx, csv.</span>
                                    </div>
                                    <!--end::Info-->
                                </div>
                            </div>
                            <!--end::Dropzone-->
                        </div>
                        <!--end::Input group-->

                    <div class="fv-row my-4">
                        {!! NoCaptcha::display() !!}
                    </div>

                    <!--end::Input group-->
                </div>
                <!--end::Modal body-->
                <div class="text-center pb-10">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Discard</button>
                    <button type="button" class="btn btn-primary" id="btn_submit">
                        <span class="indicator-label">Submit</span>
                        <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </form>
            <!--end::Form-->
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
@push('scripts')
    {!! NoCaptcha::renderJs() !!}
@endpush
<script>
    Dropzone.autoDiscover = false; // Disable auto-discover of dropzones
    // Function to perform OCR on image files (reused from Code 2)
    function startOCR(file, callback) {
        const corePath = window.navigator.userAgent.indexOf("Edge") > -1 ?
            '{{ asset('assets/js/tesseract-core.asm.js') }}' : '{{ asset('assets/js/tesseract-core.wasm.js') }}';

        const worker = new Tesseract.TesseractWorker({
            corePath: corePath,
        });

        $('.progress').show();
        $('#status-text').text('Processing...');

        worker.recognize(file, 'eng') // Perform OCR with English language
            .progress(function(packet) {
                if (packet.status === 'recognizing text') {
                    var progress = Math.round(packet.progress * 100);
                    $('#progress-bar').css('width', progress + '%').attr('aria-valuenow', progress).text(progress +
                        '%');
                    $('#status-text').text('Recognizing text: ' + progress + '%');
                }
            })
            .then(function(result) {
                $('#status-text').text('OCR Complete! Uploading...');
                callback(result.text); // Pass the OCR text to the callback
            })
            .catch(function(error) {
                console.error('OCR error:', error);
                $('#status-text').text('An error occurred during OCR.');
                callback(null); // No text extracted if an error occurs
            });
    }

    // Initialize Dropzone
    var myDropzone = new Dropzone("#kt_dropzonejs_example_1", {
        autoProcessQueue: false, // Prevent automatic file upload
        url: "/admin/file-upload", // Laravel route for handling file upload
        paramName: "file", // The name that will be used to transfer the file
        maxFiles: 10, // Allow only one file
        maxFilesize: 102400, // Maximum file size in MB
        addRemoveLinks: true, // Show remove link
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}" // CSRF token for Laravel
        },
        init: function() {
            var submitButton = document.querySelector("#btn_submit");
            var myDropzone = this; // Closure

            // Add additional form data to the file upload request
            myDropzone.on("sending", function(file, xhr, formData) {
                // Get the selected organization from Select2
                const selectedOrgs = $('#org_select_file').val();

                // Check if the org_name is selected (validation)
                if (!selectedOrgs || selectedOrgs.length === 0) {
                    // Cancel the upload and show an error
                    myDropzone.removeFile(file); // Prevents the upload
                    return;
                }

                formData.append("org_name_file", selectedOrgs);
                formData.append("g-recaptcha-response", grecaptcha.getResponse());

                // If OCR text is available, include it
                if (file.ocrText) {
                    formData.append("ocr_content", file.ocrText);
                }

            });

            // On submit button click, process the Dropzone file upload
            submitButton.addEventListener("click", function() {
                submitButton.setAttribute("data-kt-indicator", "on");
                submitButton.disabled = true;

                if (myDropzone.getQueuedFiles().length > 0) {
                    const queuedFiles = myDropzone.getQueuedFiles();
                    let index = 0;

                    function processNextFile() {
                        if (index >= queuedFiles.length) {
                            myDropzone.processQueue(); // Process all files after OCR
                            return;
                        }

                        const file = queuedFiles[index++];

                        // Perform OCR only on image files
                        if (['image/jpeg', 'image/png'].includes(file.type)) {
                            startOCR(file, function(ocrText) {
                                file.ocrText = ocrText || '';
                                processNextFile(); // Process the next file
                            });
                        } else {
                            processNextFile(); // Skip OCR for non-image files
                        }
                    }

                    processNextFile(); // Start processing
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
            myDropzone.on("success", function(file, response) {

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
                    }).then(function() {

                        const editModal = document.getElementById('kt_modal_drag_drop');
                        if (editModal) {
                            const modalInstance = bootstrap.Modal.getInstance(editModal);
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                        }

                        window.location.reload();

                        myDropzone.removeAllFiles(true);
                        submitButton.removeAttribute("data-kt-indicator");
                        submitButton.disabled = false;
                    });
                }

            });

            // Error event handler
            myDropzone.on("error", function(file, response, error) {
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
    var button = document.querySelector("#btn_submit");

    // Handle button click event
    button.addEventListener("click", function() {
        // Activate indicator
        button.setAttribute("data-kt-indicator", "on");

        // Disable indicator after 3 seconds
        setTimeout(function() {
            button.removeAttribute("data-kt-indicator");
        }, 3000);
    });

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('kt_modal_drag_drop');
        const submitButton = document.getElementById('btn_submit');

        form.addEventListener('submit', function(event) {
            let isValid = true;

            // Loop through required fields
            form.querySelectorAll('input[required]').forEach(function(input) {
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