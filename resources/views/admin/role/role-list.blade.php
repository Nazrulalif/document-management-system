@extends('layouts.user_type.auth')

@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid py-3 py-lg-8">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                <!--begin::Row-->
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-5 g-xl-9">
                    <!--begin::Col-->
                    @foreach ($roles as $role)
                    <div class="col-md-4">
                        <!--begin::Card-->
                        <div class="card card-flush h-md-100">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h2>{{ $role->role_name }}</h2>
                                </div>
                                <!--end::Card title-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-1">
                                <!--begin::Users-->
                                <div class="fw-bold text-gray-600 mb-5">Total users with this role: {{ $role->active_users_count }}</div>
                                <!--end::Users-->
                                <!--begin::Permissions-->
                                <div class="d-flex flex-column text-gray-600">
                                    @foreach ($role->listItems as $item)
                                    <div class="d-flex align-items-center py-2">
                                        <span class="bullet bg-primary me-3"></span>{{ $item }}
                                    </div>
                                    @endforeach
                                </div>
                                <!--end::Permissions-->
                            </div>
                            <!--end::Card body-->
                            <!--begin::Card footer-->
                            <div class="card-footer flex-wrap pt-0">
                                <a href="{{ route('role.view', $role->uuid) }}"
                                    class="btn btn-light btn-active-primary my-1 me-2">View Role</a>
                                <button type="button" class="btn btn-light btn-active-light-primary my-1"
                                    data-kt-docs-table-filter="edit_row" data-bs-toggle="modal"
                                    data-bs-target="#kt_modal_update_role" data-id="{{ $role->id }}">Edit Role</button>
                            </div>
                            <!--end::Card footer-->
                        </div>
                        <!--end::Card-->
                    </div>
                    @endforeach
                    <!--end::Col-->
                </div>
                
                <!--end::Row-->

                @include('admin.role.edit-role')
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->
</div>

<script src="{{ asset('assets\js\custom\apps\user-management\roles\list.js') }}"></script>

@endsection
