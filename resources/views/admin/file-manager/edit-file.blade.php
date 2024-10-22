<div class="modal fade" id="kt_modal_edit_file" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header" id="kt_modal_add_user_header">
                <!--begin::Modal title-->
                <h2 class="fw-bold">Edit File</h2>
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
                <form id="kt_modal_edit_file_form" class="form" action="{{ route('file.update', $data->uuid) }}"
                    method="post" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <!--begin::Scroll-->
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" data-kt-scroll="true"
                        data-kt-scroll-activate="true" data-kt-scroll-max-height="auto" data-kt-scroll-offset="300px">
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">Title</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" name="doc_title" id="doc_title"
                                class="form-control form-control-solid mb-3 mb-lg-0" value="{{ $data->doc_title }}"
                                required />
                            <!--end::Input-->
                            @error('doc_title')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">Description</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <textarea class="form-control form-control form-control-solid" data-kt-autosize="true"
                                id="doc_description" name="doc_description"
                                required>{{ $data->doc_description }}</textarea>
                            <!--end::Input-->
                            @error('doc_description')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row mb-7">
                            <div class="col-md-6">
                                <!--begin::Label-->
                                <label class="required fw-semibold fs-6 mb-2">Version limit</label>
                                <!--end::Label-->
                                <select id="version_limit" name="version_limit" class="form-select form-select-solid"
                                    data-control="select2" data-hide-search="true"
                                    data-placeholder="Select version limit">
                                    @for ($i = 1; $i <= 5; $i++) <option value="{{ $i }}"
                                        {{ $data->version_limit == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                </select>

                                <!--end::Input-->
                                @error('version_limit')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror

                            </div>
                            <div class="col-md-6">
                                <!--begin::Label-->
                                <label class="required fw-semibold fs-6 mb-2">Keyword</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                {{-- <input type="text" name="doc_keyword" id="doc_keyword"
                                    class="form-control form-control-solid mb-3 mb-lg-0"
                                    value='{{ $data->doc_keyword }}' required /> --}}
                                <input class="form-control form-control-solid" name="doc_keyword"
                                    value='{{ $data->doc_keyword }}' id="kt_tagify_2" />
                                <!--end::Input-->
                                @error('doc_keyword')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="fv-row mb-7">

                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">Author</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" name="doc_author" id="doc_author"
                                class="form-control form-control-solid mb-3 mb-lg-0" value="{{ $data->doc_author }}"
                                required />
                            <!--end::Input-->
                            @error('doc_author')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="fv-row mb-7">
                            <!--begin::Label-->

                            <div class="d-flex flex-stack">
                                <label class="required fw-semibold fs-6 mb-2">Summary</label>

                                @if ( $data->doc_type != 'images' && $data->doc_type != 'pptx')

                                <a type="button"
                                    class="btn btn-sm mw-120px btn-active-color-info me-5 text-info hover-scale pulse"
                                    data-bs-toggle="tooltip" title="Generate summary with AI" id="generate-summary-btn">
                                    <div id="spinner" class="spinner-border text-info" role="status" 
                                        style="--bs-spinner-width: 0.9rem; --bs-spinner-height: 0.9rem; display:none">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <i class="ki-duotone ki-abstract-24 text-info" id="icon">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Generate with AI
                                </a>
                                @endif
                            </div>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <textarea class="form-control form-control form-control-solid" data-kt-autosize="true"
                                name="doc_summary" id="doc_summary" rows="20" required>{{ $data->doc_summary }}
                            </textarea>
                            <!--end::Input-->
                            @error('doc_summary')
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
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var input2 = document.querySelector("#kt_tagify_2");
        var tagify = new Tagify(input2);

        // If you need to handle Tagify changes
        tagify.on('change', function (e) {
            console.log('Tags changed:', e.detail.value);
        });

    });

</script>
<script>
    $(document).ready(function () {
        $('#generate-summary-btn').on('click', function (e) {
            e.preventDefault();
            let uuid = "{{ $data->uuid }}"; // Get the UUID from the document data

            // Show the spinner
            $('#spinner').show();
            $('#icon').hide();

            $.ajax({
                url: `/admin/generate-summary/${uuid}`, // Your route URL
                type: 'GET', // Request type
                beforeSend: function () {
                    // Reset any previous state if needed
                    $('#doc_summary').val(''); // Optional: Clear previous summary
                },
                success: function (data) {
                    if (data.success) {
                        $('#doc_summary').val(data
                        .summary); // Populate the textarea with the generated summary
                    } else {
                        $('#doc_summary').val('Sorry, Unable to generate Summary');
                    }
                },
                error: function (xhr, status, error) {
                    $('#doc_summary').val('Sorry, Unable to generate Summary');
                },
                complete: function () {
                    // Hide the spinner after the request completes
                    $('#spinner').hide();
                    $('#icon').show();

                }
            });
        });
    });

</script>

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
        const form = document.getElementById('kt_modal_edit_file');
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
