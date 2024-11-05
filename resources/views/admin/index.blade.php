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
                <div class="row gy-5 gx-xl-10">

                    <div class="col-sm-5 col-md-6 col-xl-5 mb-xl-10">
                        <!--begin::Slider Widget 1-->
                        <div class="card card-flush h-lg-100">
                            <!--begin::Header-->
                            <div class="card-header pt-5">
                                <!--begin::Title-->
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-gray-900">Today's Highlights</span>
                                    <span class="text-gray-500 mt-1 fw-semibold fs-6">Latest statistics</span>
                                </h3>
                                <!--end::Title-->
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body pt-5">
                                <!--begin::Item-->
                                <div class="d-flex flex-stack">
                                    <!--begin::Section-->
                                    <div class="text-gray-700 fw-semibold fs-6 me-2">Total File</div>
                                    <!--end::Section-->

                                    <div class="d-flex align-items-center">
                                        @if (Auth::user()->role_guid == 1)
                                            @if($fileTrend == 'up')
                                            <i class="ki-duotone ki-arrow-up-right fs-2 text-success me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            @elseif($fileTrend == 'down')
                                            <i class="ki-duotone ki-arrow-down-right fs-2 text-danger me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            @else
                                            <i class="ki-duotone ki-minus fs-2 text-muted me-2"></i>
                                            @endif
                                        @endif
                                        <span class="text-gray-900 fw-bolder fs-6">{{ $fileCount }}</span>
                                    </div>
                                </div>
                                <!--end::Item-->
                                <!--begin::Separator-->
                                <div class="separator separator-dashed my-3"></div>
                                <!--end::Separator-->
                                <!--begin::Item-->
                                <div class="d-flex flex-stack">
                                    <!--begin::Section-->
                                    <div class="text-gray-700 fw-semibold fs-6 me-2">Total Folders</div>
                                    <!--end::Section-->

                                    <!--begin::Statistics-->
                                    <div class="d-flex align-items-center">
                                        @if (Auth::user()->role_guid == 1)

                                        @if($folderTrend == 'up')
                                        <i class="ki-duotone ki-arrow-up-right fs-2 text-success me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        @elseif($folderTrend == 'down')
                                        <i class="ki-duotone ki-arrow-down-right fs-2 text-danger me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        @else
                                        <i class="ki-duotone ki-minus fs-2 text-muted me-2"></i>
                                        @endif
                                        @endif
                                        <span class="text-gray-900 fw-bolder fs-6">{{ $folderCount }}</span>
                                    </div>
                                    <!--end::Statistics-->
                                </div>
                                <!--end::Item-->
                                @if(Auth::user()->role_guid == 1)
                                <div class="separator separator-dashed my-3"></div>
                                <div class="d-flex flex-stack">
                                    <!--begin::Section-->
                                    <div class="text-gray-700 fw-semibold fs-6 me-2">Companies</div>
                                    <!--end::Section-->

                                    <!--begin::Statistics-->
                                    <div class="d-flex align-items-center">

                                        @if($orgTrend == 'up')
                                        <i class="ki-duotone ki-arrow-up-right fs-2 text-success me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        @elseif($orgTrend == 'down')
                                        <i class="ki-duotone ki-arrow-down-right fs-2 text-danger me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        @else
                                        <i class="ki-duotone ki-minus fs-2 text-muted me-2"></i>
                                        @endif
                                        <span class="text-gray-900 fw-bolder fs-6">{{ $orgCount }}</span>
                                    </div>
                                    <!--end::Statistics-->
                                </div>
                                @endif
                                <div class="separator separator-dashed my-3"></div>
                                <div class="d-flex flex-stack">
                                    <!--begin::Section-->
                                    <div class="text-gray-700 fw-semibold fs-6 me-2">Active Users</div>
                                    <!--end::Section-->

                                    <div class="d-flex align-items-center">
                                        @if (Auth::user()->role_guid == 1)

                                        @if($userTrend == 'up')
                                        <i class="ki-duotone ki-arrow-up-right fs-2 text-success me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        @elseif($userTrend == 'down')
                                        <i class="ki-duotone ki-arrow-down-right fs-2 text-danger me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        @else
                                        <i class="ki-duotone ki-minus fs-2 text-muted me-2"></i>
                                        @endif
                                        @endif
                                        <span class="text-gray-900 fw-bolder fs-6">{{ $userCount }}</span>
                                    </div>
                                </div>

                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Slider Widget 1-->
                    </div>
                    <div class="col-sm-5 col-md-6 col-xl-5 mb-xl-10 {{ Auth::user()->role_guid==1 ? 'col-xl-5' : 'col-xl-7'}}">
                        <div class="card card-flush h-lg-100 h-md-100">
                            <!--begin::Header-->
                            <div class="card-header pt-5">
                                <!--begin::Title-->
                                <div class="card-title d-flex flex-column">
                                    <!--begin::Info-->
                                    <div class="d-flex align-items-center">
                                        <!--begin::Amount-->
                                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ number_format($totalCurrentStorage, 2) }} MB</span>
                                        <!--end::Amount-->
                                    </div>
                                    <!--end::Info-->

                                    <!--begin::Subtitle-->
                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Current usage</span>
                                    <!--end::Subtitle-->
                                </div>
                                <!--end::Title-->
                            </div>
                            <!--end::Header-->

                            <!--begin::Card body-->
                            <div class="card-body d-flex align-items-end pt-0">
                                <!--begin::Progress-->
                                <div class="d-flex align-items-center flex-column mt-3 w-100">
                                    <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                        <span class="fw-bolder fs-6 text-gray-900">
                                            {{ number_format($totalCurrentStorage, 2) }} MB of {{ number_format($totalSpace, 2) }} MB
                                        </span>
                                    </div>
                            
                                    @php
                                        // Calculate percentage of used storage
                                        $totalSpaceMB = $totalSpace; // Convert total space to MB
                                        $progress = ($totalCurrentStorage / $totalSpaceMB) * 100; // Calculate progress percentage
                                        $progress = min(max($progress, 0), 100); // Ensure progress is between 0 and 100
                                    @endphp
                            
                                    <div class="h-8px mx-3 w-100 bg-light-primary rounded">
                                        <div class="bg-primary rounded h-8px" role="progressbar" style="width: {{ $progress }}%;"
                                            aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <!--end::Progress-->
                            </div>
                            
                            <!--end::Card body-->
                        </div>
                    </div>
                    @if (Auth::user()->role_guid==1)
                    <div class="col-sm-2 col-md-6 col-xl-2 mb-xl-10 mb-5">
                        <!--begin::Slider Widget 1-->
                        <div class="card card-flush h-lg-100">
                            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                                <!--begin::Icon-->
                                <div class="m-0">
                                    <i class="ki-duotone ki-chart-simple fs-2hx text-gray-600">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </div>
                                <div class="d-flex flex-column my-7">
                                    <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2">{{ $todayLogin }}</span>
                                    <div class="m-0">
                                        <span class="fw-semibold fs-6 text-gray-500"> Today's Login </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                </div>

                <div class="fv-row">
                    <div class="card card-flush">
                        <!--begin::Header-->
                        <div class="card-header pt-5">
                            <!--begin::Title-->
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-900">Users Activity</span>
                            </h3>
                            <div class="d-flex align-items-center position-relative my-1">
                                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <input type="text" data-kt-docs-table-filter="search"
                                    class="form-control form-control-solid w-250px ps-13" placeholder="Search" />
                            </div>
                        </div>
                        <div class="card-body pt-5">
                            <table id="kt_datatable_example_1" class="table align-middle table-row-dashed fs-6 gy-5">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-12px">No</th>
                                        <th class="min-w-125px">User</th>
                                        <th class="min-w-125px">Model</th>
                                        <th class="min-w-125px">Action</th>
                                        <th class="min-w-125px">changes</th>
                                        <th class="min-w-125px">IP Address</th>
                                        <th class="min-w-125px">Created at</th>

                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">

                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->


</div>
<script src="{{ asset('assets/js/scripts.bundle.js')}}"></script>

<script>
    "use strict";

    // Class definition
    var KTDatatablesServerSide = function () {
        // Shared variables
        var table;
        var dt;
        var filterPayment;

        // Private functions
        var initDatatable = function () {
            dt = $("#kt_datatable_example_1").DataTable({
                searchDelay: 500,
                // processing: true,
                serverSide: true,
                order: [
                    [5, 'desc']
                ],
                stateSave: true,
                select: {
                    style: 'multi',
                    selector: 'td:first-child input[type="checkbox"]',
                    className: 'row-selected'
                },
                ajax: {
                    url: "/admin/dashboard",
                },
                columns: [{
                        data: null,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart +
                                1; // Column for displaying row number
                        },
                        orderable: true,
                    },
                    {
                        data: 'full_name'
                    },
                    {
                        data: 'model'
                    },
                    {
                        data: 'action',
                        render: function (data, type, row) {
                            if (data === 'Login') {
                                return '<span class="badge badge-light-success">' + data +
                                    '</span>';
                            } else if (data === 'Logout') {
                                return '<span class="badge badge-light-dark">' + data +
                                    '</span>';
                            } else if (data === 'Created') {
                                return '<span class="badge badge-light-primary">' + data +
                                    '</span>';
                            } else if (data === 'Updated') {
                                return '<span class="badge badge-light-info">' + data +
                                    '</span>';
                            } else if (data === 'Deleted') {
                                return '<span class="badge badge-light-danger">' + data +
                                    '</span>';
                            } else if (data === 'Deactivated') {
                                return '<span class="badge badge-light-danger">' + data +
                                    '</span>';
                            } else {
                                return '<span class="badge badge-light-warning">' + data +
                                    '</span>';
                            }
                        }
                    }, {
                        data: 'changes'
                    },
                    {
                        data: 'ip_address'
                    },
                    {
                        data: 'formatted_date'
                    },

                ],
                columnDefs: [{
                        data: 'org_name',
                        targets: 1,
                        className: 'text-gray-800',

                    },

                ],
                // Add data-filter attribute
                createdRow: function (row, data, dataIndex) {
                    $(row).find('td:eq(4)').attr('data-filter', data.CreditCardType);
                }
            });

            table = dt.$;

            // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
            dt.on('draw', function () {
                KTMenu.createInstances();
            });
        }

        // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
        var handleSearchDatatable = function () {
            const filterSearch = document.querySelector('[data-kt-docs-table-filter="search"]');
            filterSearch.addEventListener('keyup', function (e) {
                dt.search(e.target.value).draw();
            });
        }


        // Public methods
        return {
            init: function () {
                initDatatable();
                handleSearchDatatable();
            }
        }
    }();

    // On document ready
    KTUtil.onDOMContentLoaded(function () {
        KTDatatablesServerSide.init();
    });

</script>

@endsection
