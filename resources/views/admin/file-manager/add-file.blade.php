<div class="modal fade" id="kt_modal_upload_file" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Form-->
            <form class="form" action="none" id="kt_modal_upload_form">
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
                        <!--begin::Dropzone-->
                        <div class="dropzone dropzone-queue mb-2" id="kt_modal_upload_dropzone">
                            <!--begin::Controls-->
                            <div class="dropzone-panel mb-4">
                                <a class="dropzone-select btn btn-sm btn-primary me-2">Attach
                                    files</a>
                                <a class="dropzone-upload btn btn-sm btn-light-primary me-2">Upload
                                    All</a>
                                <a class="dropzone-remove-all btn btn-sm btn-light-primary">Remove
                                    All</a>
                            </div>
                            @if (Auth::user()->role_guid == 1)
                            <div class="form-check form-switch form-check-custom form-check-solid me-10 mb-5">
                                <input class="form-check-input h-30px w-50px" name="all_company_file" type="checkbox"
                                    value="1" id="all_company_file" />
                                <label class="form-check-label fw-semibold text-muted" for="all_company_file">
                                    All Companies
                                </label>
                            </div>
                            
                            <div id="company_selection_container_file" style="display: none;">
                                <select class="form-select form-select-solid" id="org_select_file" data-control="select2"
                                    data-close-on-select="true" data-placeholder="Select company..."
                                    data-allow-clear="true" multiple="multiple" name="org_name_file[]">
                                    @foreach ($company as $item)
                                        <option value="{{ $item->id }}">{{ $item->org_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            @endif
                            <!--end::Controls-->
                            <!-- Progress bar -->
                            <div class="progress mt-3" style="display: none">
                                <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%"
                                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                            </div>
                            <!-- Status text -->
                            <div id="status-text" class="mt-2"></div>
                            <!--begin::Items-->
                            <div class="dropzone-items wm-200px">
                                <div class="dropzone-item p-5" style="display:none">
                                    <!--begin::File-->
                                    <div class="dropzone-file">
                                        <div class="dropzone-filename text-gray-900" title="some_image_file_name.jpg">
                                            <span data-dz-name="">some_image_file_name.jpg</span>
                                            <strong>(
                                                <span data-dz-size="">340kb</span>)</strong>
                                        </div>
                                        <div class="dropzone-error mt-0" data-dz-errormessage=""></div>
                                    </div>
                                    <!--end::File-->
                                    <!--begin::Progress-->
                                    <div class="dropzone-progress">
                                        <div class="progress bg-gray-300">
                                            <div class="progress-bar bg-primary" role="progressbar" aria-valuemin="0"
                                                aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress=""></div>
                                        </div>
                                    </div>
                                    <!--end::Progress-->
                                    <!--begin::Toolbar-->
                                    <div class="dropzone-toolbar">
                                        <span class="dropzone-start">
                                            <i class="ki-duotone ki-to-right fs-1"></i>
                                        </span>
                                        <span class="dropzone-cancel" data-dz-remove="" style="display: none;">
                                            <i class="ki-duotone ki-cross fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                        <span class="dropzone-delete" data-dz-remove="">
                                            <i class="ki-duotone ki-cross fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <!--end::Toolbar-->
                                </div>
                            </div>
                            <!--end::Items-->
                        </div>
                        <!--end::Dropzone-->
                        <!--begin::Hint-->
                        <span class="form-text fs-6 text-muted">Max file size is 100MB per
                            file.</span>
                        <!--end::Hint-->
                    </div>

                    <!--end::Input group-->
                </div>
                <!--end::Modal body-->
            </form>
            <!--end::Form-->
        </div>
    </div>
</div>
<script src="{{ asset('assets/plugins/global/plugins.bundle.js')}}"></script>

<script>
     // Check initial state of the checkbox
       // Check initial state of the checkbox
    // Initialize the toggle function on page load
    toggleCompanySelectionFile();

    // Handle the checkbox change event to toggle the company selection
    $('#all_company_file').on('change', function () {
        toggleCompanySelectionFile();
    });

    // Function to show/hide the company selection dropdown
    function toggleCompanySelectionFile() {
        if ($('#all_company_file').is(':checked')) {
            // Hide the company selection if the checkbox is checked
            $('#company_selection_container_file').hide();
            // Clear any selected companies and reset the Select2 dropdown
            $('#org_select_file').val(null).trigger('change');
            $('#org_select_file').attr('required', false);
        } else {
            // Show the company selection if the checkbox is unchecked
            $('#company_selection_container_file').show();
            $('#org_select_file').attr('required', true);
        }
    }


    const id = "#kt_modal_upload_dropzone";
    const dropzone = document.querySelector(id);

    var previewNode = dropzone.querySelector(".dropzone-item");
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);

    var myDropzone = new Dropzone(id, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        url: "{{ route('file.upload') }}",
        parallelUploads: 20,
        previewTemplate: previewTemplate,
        maxFilesize: 102400, // Limit to 100MB
        autoQueue: false, // Prevent auto-upload
        previewsContainer: id + " .dropzone-items",
        clickable: id + " .dropzone-select"
    });

    myDropzone.on("addedfile", function (file) {
        const dropzoneItems = dropzone.querySelectorAll('.dropzone-item');
        dropzoneItems.forEach(dropzoneItem => {
            dropzoneItem.style.display = '';
        });
        dropzone.querySelector('.dropzone-upload').style.display = "inline-block";
        dropzone.querySelector('.dropzone-remove-all').style.display = "inline-block";
    });

    // Function to perform OCR on image files
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
                $('#progress-bar').css('width', '100%').attr('aria-valuenow', 100).text('100%');
                callback(result.text); // Pass the OCR text to the callback
            })
            .catch(function (error) {
                console.error('OCR error:', error);
                $('#status-text').text('An error occurred during OCR.');
                callback(null); // No text extracted if an error occurs
            });
    }

    // Setup the upload button to start OCR and upload process
    dropzone.querySelector(".dropzone-upload").addEventListener('click', function () {
        const files = myDropzone.getFilesWithStatus(Dropzone.ADDED);

        let remainingFiles = files.length;
        let filesProcessed = 0;

        files.forEach((file) => {
            const extension = file.name.split('.').pop().toLowerCase();
            if (['jpeg', 'jpg', 'png'].includes(extension)) {
                // Perform OCR for image files
                startOCR(file, function (ocrText) {
                    file.ocrText = ocrText; // Attach OCR result to the file
                    myDropzone.enqueueFile(file); // Enqueue the file for upload
                    filesProcessed++;
                    if (filesProcessed === remainingFiles) {
                        myDropzone
                            .processQueue(); // Start uploading when all files are processed
                    }
                });
            } else {
                // Enqueue non-image files directly
                myDropzone.enqueueFile(file);
                filesProcessed++;
                if (filesProcessed === remainingFiles) {
                    myDropzone.processQueue(); // Start uploading when all files are processed
                }
            }
        });
    });

    // Update the total progress bar
    myDropzone.on("totaluploadprogress", function (progress) {
        const progressBars = dropzone.querySelectorAll('.progress-bar');
        progressBars.forEach(progressBar => {
            progressBar.style.width = progress + "%";
        });

        $('.progress').show();
        $('#status-text').text('Processing...');

        $('#progress-bar').css('width', progress + '%').attr('aria-valuenow', progress).text(progress + '%');
    });

    myDropzone.on("sending", function (file, xhr, formData) {
        // Show the total progress bar when upload starts
        const progressBars = dropzone.querySelectorAll('.progress-bar');
        progressBars.forEach(progressBar => {
            progressBar.style.opacity = "1";
        });

        // Append OCR content to the form if available
        if (file.ocrText) {
            formData.append("ocr_content", file.ocrText);
        }

           // Add "all_company_file" field based on checkbox state
        const allCompanies = document.getElementById('all_company_file').checked;
        formData.append("all_company_file", allCompanies ? true: false);

        if (!allCompanies) {
            // If not sharing to all companies, append selected organization IDs
            const selectedOrgs = $('#org_select_file').val() || []; // Get selected values from the select2 element
            selectedOrgs.forEach(orgGuid => formData.append("org_name_file[]", orgGuid));
        }
    });

    let hasError = false; // Flag to track if any errors occur

    // Handle errors
    myDropzone.on("error", function (file, errorMessage) {
        hasError = true; // Set flag to true if an error occurs

        Swal.fire({
        text: `Upload failed for ${file.name}`,
        icon: "error",
        buttonsStyling: false,
        confirmButtonText: "Ok, got it!",
        allowOutsideClick: false, // Prevent closing by clicking outside
        allowEscapeKey: false,    // Prevent closing with the Escape key
        customClass: {
            confirmButton: "btn btn-primary"
        }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.reload(); // Reload after clicking "Ok"
            }
        });
    });

    // Hide the total progress bar when nothing's uploading anymore
    myDropzone.on("queuecomplete", function () {
        const progressBars = dropzone.querySelectorAll('.dz-complete');

        setTimeout(function () {
            progressBars.forEach(progressBar => {
                progressBar.querySelector('.progress-bar').style.opacity = "0";
                progressBar.querySelector('.progress').style.opacity = "0";
            });
            // Reload the page after all files are processed
            if (!hasError) {
                window.location.reload();
            }
        }, 500);
    });

    // Setup the button for removing all files
    dropzone.querySelector(".dropzone-remove-all").addEventListener('click', function () {
        dropzone.querySelector('.dropzone-upload').style.display = "none";
        dropzone.querySelector('.dropzone-remove-all').style.display = "none";
        myDropzone.removeAllFiles(true);
    });

    // Handle file removal
    myDropzone.on("removedfile", function () {
        if (myDropzone.files.length < 1) {
            dropzone.querySelector('.dropzone-upload').style.display = "none";
            dropzone.querySelector('.dropzone-remove-all').style.display = "none";
        }
    });

</script>
