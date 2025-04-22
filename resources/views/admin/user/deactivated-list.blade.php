@extends('admin.user.index')


<div id="kt_app_content_container" class="app-container container-fluid">
    
    @section('content-user')

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
                        class="form-control form-control-solid w-250px ps-13" placeholder="Search Users" />
                </div>
                <!--end::Search-->
            </div>
            <!--begin::Card title-->
        </div>

        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body py-4">
            <!--begin::Table-->
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_datatable_example_3">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-125px">Full Name</th>
                        <th class="min-w-125px">Email</th>
                        <th class="min-w-125px">Role</th>
                        @if(Auth::user()->role_guid == 1)
                            <th class="min-w-125px">Company</th>
                        @endif
                        <th class="min-w-125px">Last Login at</th>
                        <th class="text-end min-w-100px">Actions</th>
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
<script src="{{ asset('assets/js/scripts.bundle.js')}}"></script>
@if(Auth::user()->role_guid == 1)
<script src="{{ asset('assets/js/custom/apps/user-management/users/table-deactivated-users.js') }}"></script>
@else
<script src="{{ asset('assets/js/custom/apps/user-management/users/table-users-deactivate-diff-role.js') }}"></script>
@endif
@endsection
