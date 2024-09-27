@extends('admin.company.view-company')

@section('company_detail')
<div class="card pt-4 mb-6 mb-xl-9">
    <!--begin::Card header-->
    <div class="card-header border-0 pt-6">
        <!--begin::Card title-->
        <div class="card-title d-flex">

            <h2>User List</h2>

        </div>
        <div class="card-toolbar">
            <!--begin::Toolbar-->
            <div class="d-flex justify-content-end ">
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
                <!--end::Add user-->
            </div>
        </div>
        <!--begin::Card title-->
    </div>

    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body pt-0 pb-5">
        <input type="hidden" id="uuid" value="{{ $data->uuid }}">
        <!--begin::Table-->
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_datatable_example_2">
            <thead>
                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                    <th class="min-w-15px">No</th>
                    <th class="min-w-125px">Full Name</th>
                    <th class="min-w-105px">Email</th>
                    <th class="min-w-125px">Role</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold">

            </tbody>

        </table>
        <!--end::Table-->
    </div>
    <!--end::Card body-->
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('assets/js/scripts.bundle.js')}}"></script>
<script src="{{ asset('assets/js/custom/apps/user-management/company/table-user.js') }}"></script>

@endsection
