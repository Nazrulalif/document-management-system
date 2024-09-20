@extends('layouts.user_type.auth')

@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid py-3 py-lg-8">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                <!--begin::Card-->
                <div class="card card-flush">
                    <!--begin::Card header-->
                    <div class="card-header pt-8">
                        <div class="card-title">
                            <!--begin::Search-->
                            <div class="d-flex align-items-center position-relative my-1">
                                <i class="ki-duotone ki-magnifier fs-1 position-absolute ms-6">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <input type="text" data-kt-filemanager-table-filter="search"
                                    class="form-control form-control-solid w-250px ps-15"
                                    placeholder="Search Files & Folders" />
                            </div>
                            <!--end::Search-->
                        </div>
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end" data-kt-filemanager-table-toolbar="base">
                                <!--begin::Export-->
                                <button type="button" class="btn btn-flex btn-light-primary me-3"
                                    id="kt_file_manager_new_folder">
                                    <i class="ki-duotone ki-add-folder fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>New Folder</button>
                                    
                                <!--end::Export-->
                                <!--begin::Add customer-->
                                <button type="button" class="btn btn-flex btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#kt_modal_upload_file">
                                    <i class="ki-duotone ki-folder-up fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>Upload Files</button>
                                <!--end::Add customer-->
                            </div>
                            <!--end::Toolbar-->
                            <!--begin::Group actions-->
                            <div class="d-flex justify-content-end align-items-center d-none"
                                data-kt-filemanager-table-toolbar="selected">
                                <div class="fw-bold me-5">
                                    <span class="me-2" data-kt-filemanager-table-select="selected_count"></span>Selected
                                </div>
                                <button type="button" class="btn btn-danger"
                                    data-kt-filemanager-table-select="delete_selected"
                                    id="kt_file_manager_delete_selected">Delete
                                    Selected</button>
                            </div>

                            <!--end::Group actions-->
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body">
                        <!--begin::Table header-->
                        <div class="d-flex flex-stack">
                            <!--begin::Folder path-->
                            <div class="badge badge-lg badge-light-primary">
                                <div class="d-flex align-items-center flex-wrap">
                                    <i class="ki-duotone ki-abstract-32 fs-2 text-primary me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <a href="{{ route('dashboard.admin') }}">Home</a>
                                    <i class="ki-duotone ki-right fs-2 text-primary mx-1"></i>
                                    <a href="{{ route('fileManager.index') }}">File Manager</a>
                                    <i class="ki-duotone ki-right fs-2 text-primary mx-1"></i>
                                    @foreach($path as $folder)
                                    <a href="{{ route('folder.show', $folder->uuid) }}">{{ $folder->folder_name }}</a>
                        
                                    @if (!$loop->last)
                                        <i class="ki-duotone ki-right fs-2 text-primary mx-1"></i>
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                            <!--end::Folder path-->
                            <!--begin::Folder Stats-->
                            <div class="badge badge-lg badge-primary">
                                <span id="kt_file_manager_items_counter">0 items</span>
                            </div>
                            <!--end::Folder Stats-->
                        </div>
                        <!--end::Table header-->
                        <!--begin::Table-->
                        <table id="kt_file_manager_list" data-kt-filemanager-table="folders"
                            class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="w-10px pe-2">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                            <input class="form-check-input" type="checkbox" id="select_all" />
                                        </div>
                                    </th>
                                    <th class="min-w-250px">Name</th>
                                    <th class="min-w-10px">Upload by</th>
                                    <th class="min-w-125px">Type</th>
                                    <th class="w-125px"></th>
                                </tr>
                            </thead>
                            <tbody class="fw-semibold text-gray-600">
                                @foreach($folder->children as $childFolder)
                                <tr data-folder-id="{{ $childFolder->id }}">
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input file-manager-checkbox" data-type="folder"
                                                type="checkbox" value="{{ $childFolder->id }}" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="icon-wrapper">
                                                <i class="ki-duotone ki-folder fs-2x text-primary me-4">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>

                                            <a href="{{ route('folder.show', $childFolder->uuid) }}"
                                                class="text-gray-800 text-hover-primary">{{ $childFolder->folder_name }}</a>
                                        </div>
                                    </td>
                                    <td>{{ $childFolder->full_name }}</td>
                                    <td>File Folder</td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end">
                                            <button type="button"
                                                class="btn btn-sm btn-icon btn-light btn-active-light-primary me-2"
                                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                <i class="ki-duotone ki-dots-square fs-5 m-0">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                    <span class="path4"></span>
                                                </i>
                                            </button>
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4"
                                                data-kt-menu="true">
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('folder.show', $childFolder->uuid) }}"
                                                        class="menu-link px-3">View</a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3"
                                                        data-kt-filemanager-table="rename"
                                                        data-folder-id="{{ $childFolder->id }}">Rename</a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link text-danger px-3"
                                                        data-kt-filemanager-table-filter="delete_row"
                                                        data-folder-id="{{ $childFolder->id }}">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach

                                @foreach ($documents as $document)
                                <tr data-document-id="{{ $document->id }}">
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input file-manager-checkbox" type="checkbox"
                                                value="{{ $document->id }}" data-type="document" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="icon-wrapper">
                                                @if ( $document->doc_type == 'pdf')
                                                    <i class="fa-solid fa-file-pdf fs-2x me-4" style="color:red"></i>
                                                @elseif ($document->doc_type == 'docx' ||$document->doc_type == 'doc' )
                                                    <i class="fas fa-file-word text-primary fs-2x me-4"></i>
                                                @elseif ( $document->doc_type == 'xlsx' || $document->doc_type == 'csv' )
                                                <i class="fas fa-file-excel fs-2x me-4"  style="color:green"></i>
                                                @elseif ( $document->doc_type == 'pptx' )
                                                    <i class="fa-solid fa-file-powerpoint fs-2x me-4" style="color: orange"></i>
                                                @elseif ( $document->doc_type == 'images' )
                                                    <i class="fa-solid fa-image text-gray-800 fs-2x me-4"></i>
                                                @endif
                                            </span>
                                            <a href="{{ route('file.index', $document->latest_version_guid)}}"
                                                class="text-gray-800 text-hover-primary">{{ $document->doc_title }}</a>
                                        </div>
                                    </td>
                                    <td>{{$document->full_name}}</td>
                                    <td>{{$document->doc_type}}</td>
                                    <td>
                                        <div class="d-flex justify-content-end">
                                            <button type="button"
                                                class="btn btn-sm btn-icon btn-light btn-active-light-primary me-2"
                                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                <i class="ki-duotone ki-dots-square fs-5 m-0">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                    <span class="path4"></span>
                                                </i>
                                            </button>
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4"
                                                data-kt-menu="true">
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('file.index', $document->latest_version_guid)}}" class="menu-link px-3">View</a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link text-danger px-3"
                                                        data-kt-filemanager-table-filter="delete_row_document"
                                                        data-document-id="{{ $document->id }}">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!--end::Table-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
                <!--begin::Upload template-->
                <table class="d-none">
                    <tr id="kt_file_manager_new_folder_row" data-kt-filemanager-template="upload">
                        <td></td>
                        <td id="kt_file_manager_add_folder_form" class="fv-row">
                            <form action="{{ route('folder.create') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="d-flex align-items-center">
                                    <!--begin::Folder icon-->
                                    <span id="kt_file_manager_folder_icon">
                                        <i class="ki-duotone ki-folder fs-2x text-primary me-4">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <!--end::Folder icon-->

                                    <!-- Input for new folder name -->
                                    <input type="hidden" name="new_folder_id"
                                                        class="form-control mw-250px me-3" value="{{ $folder->id }}" />
                                    <input type="text" name="new_folder_name" placeholder="Enter the folder name"
                                        class="form-control mw-250px me-3" />

                                    <!-- Submit button -->
                                    <button class="btn btn-icon btn-light-primary me-3" type="submit">
                                        <span class="indicator-label">
                                            <i class="ki-duotone ki-check fs-1"></i>
                                        </span>
                                        <span class="indicator-progress">
                                            <span class="spinner-border spinner-border-sm align-middle"></span>
                                        </span>
                                    </button>

                                    <!-- Cancel button with id added -->
                                    <button type="button" class="btn btn-icon btn-light-danger"
                                        id="kt_file_manager_cancel_button">
                                        <span class="indicator-label">
                                            <i class="ki-duotone ki-cross fs-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                        <span class="indicator-progress">
                                            <span class="spinner-border spinner-border-sm align-middle"></span>
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>

                <!--end::Upload template-->
                <!--begin::Rename template-->
                <div class="d-none" data-kt-filemanager-template="rename">
                    <div class="fv-row">
                        <div class="d-flex align-items-center">
                            <span id="kt_file_manager_rename_folder_icon"></span>
                            <input type="text" id="kt_file_manager_rename_input" name="rename_folder_name"
                                placeholder="Enter the new folder name" class="form-control mw-250px me-3" value="" />
                            <button class="btn btn-icon btn-light-primary me-3" id="kt_file_manager_rename_folder"
                                type="button">
                                <i class="ki-duotone ki-check fs-1"></i>
                            </button>
                            <button class="btn btn-icon btn-light-danger" id="kt_file_manager_rename_folder_cancel"
                                type="button">
                                <span class="indicator-label">
                                    <i class="ki-duotone ki-cross fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <span class="indicator-progress">
                                    <span class="spinner-border spinner-border-sm align-middle"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <!--end::Rename template-->

                <!--begin::Modals-->
                <!--begin::Modal - Upload File-->
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
                                            <input type="hidden" name="folder_id" id="folder_id" value="{{ $folder->id }}">

                                            <!--begin::Controls-->
                                            <div class="dropzone-panel mb-4">
                                                <a class="dropzone-select btn btn-sm btn-primary me-2">Attach
                                                    files</a>
                                                <a class="dropzone-upload btn btn-sm btn-light-primary me-2">Upload
                                                    All</a>
                                                <a class="dropzone-remove-all btn btn-sm btn-light-primary">Remove
                                                    All</a>
                                            </div>
                                            <!--end::Controls-->
                                            <!-- Progress bar -->
                                            <div class="progress mt-3" style="display: none">
                                                <div id="progress-bar" class="progress-bar" role="progressbar"
                                                    style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                                                    aria-valuemax="100">0%</div>
                                            </div>
                                            <!-- Status text -->
                                            <div id="status-text" class="mt-2"></div>
                                            <!--begin::Items-->
                                            <div class="dropzone-items wm-200px">
                                                <div class="dropzone-item p-5" style="display:none">
                                                    <!--begin::File-->
                                                    <div class="dropzone-file">
                                                        <div class="dropzone-filename text-gray-900"
                                                            title="some_image_file_name.jpg">
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
                                                            <div class="progress-bar bg-primary" role="progressbar"
                                                                aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"
                                                                data-dz-uploadprogress=""></div>
                                                        </div>
                                                    </div>
                                                    <!--end::Progress-->
                                                    <!--begin::Toolbar-->
                                                    <div class="dropzone-toolbar">
                                                        <span class="dropzone-start">
                                                            <i class="ki-duotone ki-to-right fs-1"></i>
                                                        </span>
                                                        <span class="dropzone-cancel" data-dz-remove=""
                                                            style="display: none;">
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
                                        <span class="form-text fs-6 text-muted">Max file size is 1MB per
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
                <!--end::Modal - Upload File-->

                <!--end::Modals-->
            </div>

            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->

</div>
<script>
    var hostUrl = '/assets/';

</script>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>

<script src="{{ asset('assets/plugins/global/plugins.bundle.js')}}"></script>
<script src="{{ asset('assets/js/scripts.bundle.js')}}"></script>
<script src="{{ asset('assets/js/custom/apps/file-manager/listss.js')}}"></script>
<script src="{{ asset('assets/js/custom/apps/file-manager/lists-2.js')}}"></script>


{{-- Upload file dropzone --}}
<script>
    const id = "#kt_modal_upload_dropzone";
    const dropzone = document.querySelector(id);

    var previewNode = dropzone.querySelector(".dropzone-item");
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);

    const folderId = document.querySelector('#folder_id').value;

    var myDropzone = new Dropzone(id, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        url: "{{ route('file.upload') }}",
        parallelUploads: 20,
        previewTemplate: previewTemplate,
        maxFilesize: 1, // Limit to 1MB
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
            '{{ asset('assets/js/tesseract-core.asm.js') }}' : '{{ asset('assets/js/tesseract-core.wasm.js') }}';

        const worker = new Tesseract.TesseractWorker({
            corePath: corePath,
        });

        $('.progress').show();
        $('#status-text').text('Processing...');

        worker.recognize(file, 'eng') // Perform OCR with English language
            .progress(function (packet) {
                if (packet.status === 'recognizing text') {
                    var progress = Math.round(packet.progress * 100);
                    $('#progress-bar').css('width', progress + '%').attr('aria-valuenow', progress).text(progress + '%');
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
                        myDropzone.processQueue(); // Start uploading when all files are processed
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
        formData.append('folder_id', folderId);

        // Show the total progress bar when upload starts
        const progressBars = dropzone.querySelectorAll('.progress-bar');
        progressBars.forEach(progressBar => {
            progressBar.style.opacity = "1";
        });

        // Append OCR content to the form if available
        if (file.ocrText) {
            formData.append("ocr_content", file.ocrText);
        }
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
            window.location.reload();
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

@endsection
