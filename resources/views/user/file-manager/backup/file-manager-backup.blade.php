@extends('layouts.user_type.auth')

@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        @include('layouts.appToolbar-user')
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid py-3 py-lg-8">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                <!--begin::Card-->
                <div class="card card-flush">
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
                                    <a href="{{ route('home.user') }}">Home</a>
                                    <i class="ki-duotone ki-right fs-2 text-primary mx-1"></i>
                                    <a href="{{ route('file-manager.user') }}">File Manager</a>
                                </div>
                            </div>
                            <!--end::Folder path-->
                            <!--begin::Folder Stats-->
                            <div class="badge badge-lg badge-primary">
                                <span id="kt_file_manager_items_counter">
                                    {{ $folders->count() + $documents->whereNull('folder_guid')->count() }} items</span>
                            </div>
                            <!--end::Folder Stats-->
                        </div>
                        <!--end::Table header-->
                        <!--begin::Table-->
                        <div class="table-responsive">

                            <table class="table align-middle table-row-dashed fs-6" id="kt_datatable_zero_configuration">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">

                                        <th class="min-w-200px">Name</th>
                                        <th class="min-w-250px">Company</th>
                                        <th class="min-w-10px">Upload by</th>
                                        <th class="min-w-125px">Type</th>
                                        <th class="min-w-125px">Add to starred</th>
                                        <th class="w-10px"></th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @if($folders->count() > 0 || $documents->count() > 0)
                                    @foreach ($folders as $folder)
                                    <tr data-folder-id="{{ $folder->id }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="icon-wrapper">
                                                    <i class="ki-duotone ki-folder fs-2x text-primary me-4">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </span>
                                                <a href="{{ route('folder.show.user', $folder->uuid) }}"
                                                    class="text-gray-800 text-hover-primary">
                                                    {{ $folder->folder_name }}
                                                </a>
                                            </div>
                                        </td>
                                        <td>{{ $folder->org_name }}</td>
                                        <td>{{ $folder->full_name }}</td>
                                        <td>File Folder</td>
                                        <td class="text-center">
                                            @if(in_array($folder->id, $starredFolders))
                                            
                                            <i class="ki-duotone ki-star fs-3 star-icon text-warning cursor-pointer"
                                                data-id="{{ $folder->id }}" data-type="folder"></i> <!-- Starred -->
                                            @else
                                            <i class="ki-outline ki-star fs-3 star-icon cursor-pointer" data-id="{{ $folder->id }}"
                                                data-type="folder"></i> <!-- Not starred -->
                                            @endif
                                        </td>

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
                                                        <a href="{{ route('folder.show.user', $folder->uuid) }}"
                                                            class="menu-link px-3">View</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach

                                    @foreach ($documents as $document)
                                    @if (is_null($document->folder_guid))
                                    <tr data-document-id="{{ $document->id }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="icon-wrapper">
                                                    @if ($document->doc_type == 'pdf')
                                                    <img src="{{ asset('assets/media/icons/duotune/files/pdf-file.png') }}"
                                                        class="mw-30px me-4" />
                                                    @elseif ($document->doc_type == 'docx' || $document->doc_type ==
                                                    'doc')
                                                    <img src="{{ asset('assets/media/icons/duotune/files/word-file.png') }}"
                                                        class="mw-30px me-4" />
                                                    @elseif ($document->doc_type == 'xlsx' || $document->doc_type ==
                                                    'csv')
                                                    <img src="{{ asset('assets/media/icons/duotune/files/excel-file.png') }}"
                                                        class="mw-30px me-4" />
                                                    @elseif ($document->doc_type == 'pptx')
                                                    <img src="{{ asset('assets/media/icons/duotune/files/pptx-file.png') }}"
                                                        class="mw-30px me-4" />
                                                    @elseif ($document->doc_type == 'images')
                                                    <img src="{{ asset('assets/media/icons/duotune/files/image-file.png') }}"
                                                        class="mw-30px me-4" />
                                                    @endif
                                                </span>
                                                <a href="{{ route('file.user', $document->latest_version_guid) }}"
                                                    class="text-gray-800 text-hover-primary">
                                                    {{ $document->doc_title }}
                                                </a>
                                            </div>
                                        </td>
                                        <td>{{ $document->org_name }}</td>
                                        <td>{{ $document->full_name }}</td>
                                        <td>{{ $document->doc_type }}</td>
                                        <td class="text-center">
                                            

                                                @if(in_array($document->id, $starredDoc))
                                            
                                                <i class="ki-duotone ki-star fs-3 star-icon text-warning cursor-pointer"
                                                    data-id="{{ $document->id }}" data-type="document"></i> <!-- Starred -->
                                                @else
                                                <i class="ki-outline ki-star fs-3 star-icon cursor-pointer"
                                                data-id="{{ $document->id }}" data-type="document"></i>
                                                @endif
                                        </td>
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
                                                        <a href="{{ route('file.index', $document->latest_version_guid) }}"
                                                            class="menu-link px-3">View</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <img src="{{ asset('assets/media/illustrations/sketchy-1/4.png') }}"
                                                class="mw-300px" alt="">
                                            <div class="fs-1 fw-bolder text-dark">No files found.</div>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>

                            </table>
                            <!--end::Table-->
                        </div>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
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

<script>
    $(document).ready(function () {
        $("#kt_datatable_zero_configuration").DataTable({
            language: {
                emptyTable: "No files or documents found.",
                zeroRecords: "No matching records found.",
                infoEmpty: "Showing 0 to 0 of 0 entries",
            },
            columnDefs: [
                { targets: "_all", defaultContent: "" } // Ensure no missing content errors
            ],
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50]
        });
    });
    
</script>
<script>
    $(document).on('click', '.star-icon', function () {

        const element = $(this);
        const id = element.data('id');
        const type = element.data('type');

        $.ajax({
            url: `/star`, // Adjust this URL as per your route
            type: 'POST',
            data: {
                id: id,
                type: type,
                _token: "{{ csrf_token() }}",
            },
            success: function(response) {
            if (response.success) {
                if (response.starred) {
                    // If starred, switch to the duotone style and add text-warning class
                    element.removeClass('ki-outline').addClass('ki-duotone text-warning');
                } else {
                    // If unstarred, switch to the outline style and remove text-warning class
                    element.removeClass('ki-duotone text-warning').addClass('ki-outline');
                }
            }
        },
            error: function () {
                alert('Something went wrong. Please try again.');
            }
        });
    });

</script>
@endsection
