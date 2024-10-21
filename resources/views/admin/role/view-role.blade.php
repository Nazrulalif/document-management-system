@extends('layouts.user_type.auth')

@section('content')
<div id="role-data" data-uuid="{{ $role_user->uuid }}"></div>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid py-3 py-lg-8">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                <!--begin::Layout-->
                <div class="d-flex flex-column flex-lg-row">
                    <!--begin::Sidebar-->
                    <div class="flex-column flex-lg-row-auto w-100 w-lg-200px w-xl-300px mb-10">
                        <!--begin::Card-->
                        <div class="card card-flush">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h2 class="mb-0">{{ $role_user->role_name }}</h2>
                                </div>
                                <!--end::Card title-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <!--begin::Permissions-->
                                <div class="d-flex flex-column text-gray-600">
                                    @foreach ($role_user->listItems as $item)
                                        <div class="d-flex align-items-center py-2">
                                            <span class="bullet bg-primary me-3"></span>{{ $item }}
                                        </div>
                                    @endforeach
                                </div>
                                <!--end::Permissions-->
                            </div>
                            <!--end::Card body-->
                            <!--begin::Card footer-->
                            <div class="card-footer pt-0">
                                <button type="button" class="btn btn-light btn-active-light-primary my-1"
                                    data-kt-docs-table-filter="edit_row" data-bs-toggle="modal"
                                    data-bs-target="#kt_modal_update_role" data-id="{{ $role_user->id }}">Edit
                                    Role</button>
                            </div>

                            <!--end::Card footer-->
                        </div>
                        <!--end::Card-->
                        <!--begin::Modal-->
                        <!--begin::Modal - Update role-->
                        @include('admin.role.edit-role')
                        @include('admin.user.edit-user')

                        <!--end::Modal - Update role-->
                        <!--end::Modal-->
                    </div>
                    <!--end::Sidebar-->
                    <!--begin::Content-->
                    <div class="flex-lg-row-fluid ms-lg-10">
                        <!--begin::Card-->
                        <div class="card card-flush mb-6 mb-xl-9">
                            <!--begin::Card header-->
                            <div class="card-header pt-5">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h2 class="d-flex align-items-center">Users Assigned
                                        <span class="text-gray-600 fs-6 ms-1">( {{ $role_user->active_users_count }} )</span></h2>
                                </div>
                                <!--end::Card title-->
                                
                                <!--begin::Card toolbar-->
                                <div class="card-toolbar">
                                    <!--begin::Search-->
                                    <div class="d-flex align-items-center position-relative my-1"
                                    data-kt-docs-table-toolbar="base">
                                        <i class="ki-duotone ki-magnifier fs-1 position-absolute ms-6">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <input type="text" data-kt-docs-table-filter="search"
                                            class="form-control form-control-solid w-250px ps-15"
                                            placeholder="Search Users" />
                                    </div>
                                    <!--end::Search-->
                                    <!--begin::Group actions-->
                                    <div class="d-flex justify-content-end align-items-center d-none"
                                    data-kt-docs-table-toolbar="selected">
                                        <div class="fw-bold me-5">
                                            <span class="me-2"
                                            data-kt-docs-table-select="selected_count"></span>Selected
                                        </div>
                                        <button type="button" class="btn btn-danger"
                                            data-kt-docs-table-select="delete_selected">Deactive
                                            Selected</button>
                                    </div>
                                    <!--end::Group actions-->
                                </div>
                                <!--end::Card toolbar-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <!--begin::Table-->
                                <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0"
                                    id="kt_roles_view_table">
                                    <thead>
                                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                            <th class="min-w-10px">No</th>
                                            <th class="min-w-150px">User</th>
                                            <th class="min-w-150px">Email</th>
                                            <th class="min-w-125px">Joined Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600">
                                    </tbody>
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Layout-->
            </div>

            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->


</div>
<script src="{{ asset('assets/js/scripts.bundle.js')}}"></script>
<script src="{{ asset('assets\js\custom\apps\user-management\roles\list.js') }}"></script>
<script src="{{ asset('assets/js/custom/apps/user-management/roles/view.js') }}"></script>


@endsection
