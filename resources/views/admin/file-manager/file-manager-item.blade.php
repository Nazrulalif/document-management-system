@extends('layouts.user_type.auth')

@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid py-3 py-lg-8">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-fluid">
                <div class="card">
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-6">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <!--begin::Search-->
                            <div class="d-flex align-items-center position-relative my-1">
                                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <input type="text" data-kt-docs-table-filter="search"
                                    class="form-control form-control-solid w-250px ps-13" placeholder="Search Folder and Files" />
                            </div>
                            <!--end::Search-->
                        </div>
                        <!--begin::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end" data-kt-docs-table-toolbar="base">
                                 <button type="button" class="btn btn-flex btn-light-primary me-3"
                                    id="new_folder"  data-bs-toggle="modal"
                                    data-bs-target="#kt_modal_add_folder">
                                    <i class="ki-duotone ki-add-folder fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>New Folder</button>
                                <!--begin::Add user-->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#kt_modal_upload_file">
                                    <i class="ki-duotone ki-folder-up fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>Upload Files</button>
                                <!--end::Add user-->
                            </div>
                            <!--end::Toolbar-->
                            <!--begin::Group actions-->
                            <div class="d-flex justify-content-end align-items-center d-none"
                                data-kt-docs-table-toolbar="selected">
                                <div class="fw-bold me-5">
                                    <span class="me-2" data-kt-docs-table-select="selected_count"></span>Selected</div>
                                <button type="button" class="btn btn-danger"
                                    data-kt-docs-table-select="delete_selected">Delete Selected</button>
                            </div>
                            <!--end::Group actions-->
                            <!--begin::Modal - Add task-->
                            @include('admin.file-manager.add-folder-item')
                            @include('admin.file-manager.edit-folder')
                            @include('admin.file-manager.add-file-item')
                            <!--end::Modal - Add task-->
                        </div>
                        <!--end::Card toolbar-->
                    </div>

                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body py-4">
                        <div class="d-flex flex-stack">
                            <!--begin::Folder path-->
                            <div class="badge badge-lg badge-light-primary">
                                <div class="d-flex align-items-center flex-wrap">
                                    <i class="ki-duotone ki-abstract-32 fs-2 text-primary me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
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
                        <!--begin::Table-->
                        <input type="hidden" value="{{$uuid }}" id="uuid-folder" readonly>
                        
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_datatable_example_2">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th class="w-10px pe-2">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                            <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                data-kt-check-target="#kt_datatable_example_2 .form-check-input"
                                                value="1" />
                                        </div>
                                    </th>
                                    <th class="min-w-125px">Name</th>
                                    <th class="min-w-125px">Company</th>
                                    <th class="min-w-125px">Upload by</th>
                                    <th class="min-w-125px">Add to starred</th>
                                    <th class="text-end min-w-100px"></th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">

                            </tbody>
                        </table>
                        <!--end::Table-->
                    </div>
                    <!--end::Card body-->
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
<script src="{{ asset('assets/js/custom/apps/file-manager/table-file-item.js') }}"></script>
@endsection
