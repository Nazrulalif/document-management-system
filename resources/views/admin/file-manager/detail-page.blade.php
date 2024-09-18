@extends('layouts.user_type.auth')

@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid py-3 py-lg-8">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card card-flush">
                    <div class="card-header px-lg-17">
                        <h3 class="card-title"></h3>
                        <div class="card-toolbar">
                            <div class="p-1">
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                                    data-bs-target="#kt_modal_edit_file">
                                    Edit
                                </button>
                            </div>
                            <div class="p-1">
                                {{-- <a type="button" class="btn btn-sm btn-danger" id="delete_button" href="{{ route('file.detail.destroy', $data->uuid) }}">
                                Delete
                                </a> --}}
                                <button type="button" class="btn btn-sm btn-danger" data-kt-filter="delete_file"
                                    data-uuid="{{ $data->uuid }}">
                                    Delete
                                </button>
                            </div>


                        </div>
                    </div>

                    @include('admin.file-manager.edit-file')
                    @include('admin.file-manager.add-version')
                    @include('admin.file-manager.view-version')

                    <!--begin::Body-->
                    <div class="card-body p-lg-17 pt-lg-3">

                        <!--begin::Layout-->
                        <div class="d-flex flex-column flex-lg-row mb-17">
                            <!--begin::Content-->
                            <div class="flex-lg-row-fluid me-0 me-lg-20">
                                <!--begin::Job-->
                                <div class="mb-17">
                                    <!--begin::Description-->
                                    <div class="m-0">
                                        <span class="badge badge-light-success">Latest</span>
                                        <!--begin::Title-->
                                        <h4 class="fs-1 text-gray-800 w-bolder mb-6">{{ $data->doc_title }}</h4>
                                        <!--end::Title-->
                                        <!--begin::Text-->
                                        <p class="fw-semibold fs-4 text-gray-600 mb-2">{{ $data->doc_description }}</p>
                                        <!--end::Text-->
                                    </div>
                                    <!--end::Description-->
                                    <!--begin::Accordion-->
                                    <!--begin::Section-->

                                    @if ($data->doc_type == 'images')
                                    <div class="d-flex flex-end">
                                        <a href="{{ asset('storage/' . $data->file_path) }}"
                                            download="{{ basename($data->file_path) }}" target="_blank"
                                            class="btn btn-sm btn-primary mt-5">
                                            Download
                                        </a>
                                    </div>
                                    @else
                                    <div class="d-flex flex-end">
                                        <a href="{{ asset('storage/' . $data->file_path) }}" target="_blank"
                                            class="btn btn-sm btn-primary mt-5">
                                            Download
                                        </a>
                                    </div>
                                    @endif

                                    <div class="m-0">
                                        <!--begin::Heading-->
                                        <div class="d-flex align-items-center collapsible py-3 toggle mb-0"
                                            data-bs-toggle="collapse" data-bs-target="#kt_job_1_1">
                                            <!--begin::Icon-->
                                            <div class="btn btn-sm btn-icon mw-20px btn-active-color-primary me-5">
                                                <i class="ki-duotone ki-minus-square toggle-on text-primary fs-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <i class="ki-duotone ki-plus-square toggle-off fs-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                            </div>
                                            <!--end::Icon-->
                                            <!--begin::Title-->
                                            <h4 class="text-gray-700 fw-bold cursor-pointer mb-0">
                                                Summary</h4>
                                            <!--end::Title-->
                                        </div>
                                        <!--end::Heading-->
                                        <!--begin::Body-->
                                        <div id="kt_job_1_1" class="collapse show fs-6 ms-1">
                                            <!--begin::Item-->
                                            <div class="mb-4">
                                                <!--begin::Item-->
                                                <!--begin::Label-->
                                                <div class="text-gray-600 fw-semibold fs-6">
                                                    @foreach ($doc_summary as $line)
                                                    <p>{{ $line }}</p> <!-- Display each line inside a paragraph -->
                                                    @endforeach
                                                </div>
                                                <!--end::Label-->
                                                <!--end::Item-->
                                            </div>
                                            <!--end::Item-->

                                        </div>
                                        <!--end::Content-->
                                        <!--begin::Separator-->
                                        <div class="separator separator-dashed"></div>
                                        <!--end::Separator-->
                                    </div>
                                    <!--end::Section-->
                                    <!--begin::Section-->
                                    <div class="m-0">
                                        <!--begin::Heading-->
                                        <div class="d-flex align-items-center collapsible py-3 toggle mb-0"
                                            data-bs-toggle="collapse" data-bs-target="#kt_job_1_2">
                                            <!--begin::Icon-->
                                            <div class="btn btn-sm btn-icon mw-20px btn-active-color-primary me-5">
                                                <i class="ki-duotone ki-minus-square toggle-on text-primary fs-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <i class="ki-duotone ki-plus-square toggle-off fs-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                            </div>
                                            <!--end::Icon-->
                                            <!--begin::Title-->
                                            <h4 class="text-gray-700 fw-bold cursor-pointer mb-0">Others</h4>
                                            <!--end::Title-->
                                        </div>
                                        <!--end::Heading-->
                                        <!--begin::Body-->
                                        <div id="kt_job_1_2" class="collapse show fs-6 ms-1">
                                            <!--begin::Item-->
                                            <div class="mb-4">
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <tbody>
                                                            <tr>
                                                                <td class="fw-bold fs-6 text-gray-800">Authors:&nbsp;
                                                                </td>
                                                                <td class="text-gray-600 fw-semibold fs-6">
                                                                    {{ $data->doc_author }}</span></a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold fs-6 text-gray-800">Upload by:&nbsp;
                                                                </td>
                                                                <td class="text-gray-600 fw-semibold fs-6">
                                                                    {{ $data->full_name }}</span></a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold fs-6 text-gray-800">Company:&nbsp;
                                                                </td>
                                                                <td class="text-gray-600 fw-semibold fs-6">
                                                                    {{ $data->org_name }}</span></a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold fs-6 text-gray-800">Keyword:&nbsp;
                                                                </td>
                                                                <td class="text-gray-600 fw-semibold fs-6">
                                                                    {{ $data->doc_keyword }}</span></a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold fs-6 text-gray-800">File type:&nbsp;
                                                                </td>
                                                                <td class="text-gray-600 fw-semibold fs-6">
                                                                    {{ $data->doc_type }}</span></a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold fs-6 text-gray-800">Created at:&nbsp;
                                                                </td>
                                                                <td class="text-gray-600 fw-semibold fs-6">
                                                                    {{ $data->created_at->format('d-m-Y') }}</span></a>
                                                                </td>
                                                            </tr>


                                                        </tbody>
                                                    </table>
                                                </div>

                                            </div>
                                            <!--end::Item-->

                                        </div>
                                        <!--end::Content-->
                                        <!--begin::Separator-->
                                        <div class="separator separator-dashed"></div>
                                        <!--end::Separator-->
                                    </div>
                                    <!--end::Section-->

                                    <!--end::Accordion-->
                                    <!--begin::Apply-->
                                    <!--end::Apply-->
                                </div>
                                <!--end::Job-->

                            </div>
                            <!--end::Content-->
                            <!--begin::Sidebar-->
                            <div class="flex-lg-row-auto w-100 w-lg-275px w-xxl-350px">
                                @if ( $data->doc_type == 'images')
                                <div class="card bg-light mb-3">
                                    <!--begin::Body-->
                                    <div class="card-body">
                                        <div class="d-flex flex-center">
                                            <img src="{{ asset('assets/media/misc/spinner.gif') }}"
                                                data-src="{{ asset('storage/' . $data->file_path) }}"
                                                class="lozad rounded " alt=""
                                                style="max-width: -webkit-fill-available;" />
                                        </div>

                                    </div>
                                    <!--end::Body-->
                                </div>
                                @endif

                                <!--begin::Careers about-->
                                <div class="card bg-light">
                                    <!--begin::Body-->
                                    <div class="card-body">
                                        <!--begin::Top-->
                                        <div class="mb-7">
                                            <!--begin::Title-->
                                            <div class="d-flex" style="justify-content: space-between;">
                                                <h2 class="fs-1 text-gray-800 w-bolder mb-6">Versions</h2>
                                                <button type="button"
                                                    class="btn btn-sm btn-icon mw-20px btn-active-color-primary me-5"
                                                    data-bs-toggle="modal" data-bs-target="#kt_modal_add_version">
                                                    <i class="ki-duotone ki-plus-square fs-2x">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </button>

                                            </div>

                                            <!--end::Title-->
                                        </div>
                                        <!--end::Top-->
                                        <!--begin::Item-->
                                        <div class="mb-8">
                                            <!--begin::Timeline-->
                                            <div class="timeline-label">
                                                @foreach ($version as $item )
                                                <!--begin::Item-->
                                                <div class="timeline-item">
                                                    <!--begin::Label-->
                                                    <div class="timeline-label fw-bold text-gray-800 fs-6">
                                                        {{$item->version_number}}
                                                    </div>
                                                    <!--end::Label-->
                                                    <!--begin::Badge-->
                                                    <div class="timeline-badge">
                                                        <i class="ki-duotone ki-abstract-8 text-gray-600 fs-3">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </div>
                                                    <!--end::Badge-->
                                                    <!--begin::Text-->
                                                    <div class="d-flex flex-column">

                                                        <div class="timeline-content fw-bold text-primary ps-3">
                                                            @if($loop->first)
                                                            <a>{{($item->change_title ? $item->change_title : 'First Version')}}</a>
                                                            @else
                                                            <a type="button" data-bs-toggle="modal"
                                                                data-bs-target="#kt_modal_view_version"
                                                                data-version-number="{{ $item->version_number }}"
                                                                data-change-title="{{ $item->change_title ? $item->change_title : 'First Version' }}"
                                                                data-change-description="{{ $item->change_description }}"
                                                                data-created-by="{{ $item->full_name }}"
                                                                data-file="{{ asset('storage/' . $item->file_path) }}"
                                                                data-created-at="{{ $item->created_at->format('d-m-Y') }}">{{($item->change_title ? $item->change_title : 'First Version')}}</a>
                                                            @endif
                                                        </div>
                                                        <div class="fw-semibold text-gray-700 ps-3 fs-7">
                                                            {{$item->created_at->format('d-m-Y')}}
                                                            @if($loop->first)
                                                            <!-- Display the "Latest" badge only for the latest version -->
                                                            <span class="badge badge-light-success">Latest</span>
                                                            @endif

                                                        </div>

                                                    </div>

                                                    <!--end::Text-->
                                                </div>
                                                <!--end::Item-->
                                                @endforeach


                                            </div>
                                            <!--end::Timeline-->


                                        </div>
                                        <!--end::Item-->

                                    </div>
                                    <!--end::Body-->
                                </div>
                                <!--end::Careers about-->
                            </div>
                            <!--end::Sidebar-->
                        </div>
                        <!--end::Layout-->
                    </div>
                    <!--end::Body-->
                </div>
            </div>

            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->

</div>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>

<script src="{{ asset('assets/plugins/global/plugins.bundle.js')}}"></script>
<script src="{{ asset('assets/js/scripts.bundle.js')}}"></script>
<script>
    $(document).ready(function () {
        $('#kt_modal_view_version').on('show.bs.modal', function (event) {
            // Button that triggered the modal
            var button = $(event.relatedTarget);

            // Extract info from data-* attributes
            var versionTitle = button.data('change-title');
            var versionDescription = button.data('change-description');
            var createdBy = button.data('created-by');
            var file = button.data('file');

            // Update the modal's content
            var modal = $(this);
            modal.find('#modalVersionTitle').text(versionTitle);
            modal.find('#modalVersionDescription').text(versionDescription);
            modal.find('#modalCreatedBy').text(createdBy);
            modal.find('#modalfile').attr('href', file);
        });
    });

</script>
<script>
    $(document).ready(function () {
        // Handle delete button click
        $(document).on('click', '[data-kt-filter="delete_file"]', function (e) {
            e.preventDefault();

            var fileUuid = $(this).data('uuid');
            // SweetAlert confirmation dialog
            Swal.fire({
                text: "Are you sure you want to delete this version file?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Yes, delete!",
                cancelButtonText: "No, cancel",
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then((result) => {

                if (result.value) {

                    // Send the delete request to the server
                    $.ajax({
                        url: `/admin/file-detail-destroy/` + fileUuid, // Assuming RESTful API convention
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            // Show success message
                            // console.log(response);
                            Swal.fire({
                                text: "You have deleted the version file!",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            }).then(function () {
                                if (response.success && response.uuid) {
                                    window.location.href = "/admin/file-details/" + response.uuid;
                                } else {
                                    window.location.href = "/admin/file-manager";
                                }

                            });

                        },
                        error: function (xhr) {
                            // Handle the error
                            Swal.fire({
                                text: "There was an error deleting the version file. Please try again.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        }
                    });
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: "The version file was not deleted .",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }
            });
        });

    })

</script>
@endsection
