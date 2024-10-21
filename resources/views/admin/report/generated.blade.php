@extends('layouts.user_type.auth')

@section('content')
<style>
    @media print {
        .page-break {
            page-break-before: always; /* Start a new page before this element */
        }
       
    }
</style>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid py-3 py-lg-8">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-fluid">
                <div class="card">

                    <div class="card-body py-20 pt-10" id="printable-content">
                        <div class="d-flex justify-content-end mt-3 pb-5">
                            <a class="btn btn-flex btn-light-secondary" id="print-btn">
                                <i class="ki-duotone ki-printer fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                                Print
                            </a>
                        </div>
                        <div class="mw-lg-950px mx-auto w-100">
                            <div class="d-flex d-sm-flex flex-column align-items-center mb-19">
                                <h4 class="fw-bolder text-gray-800 fs-2qx">Document Management System Report</h4>
                                <div class=" fw-semibold fs-4 text-muted mt-2">Overview of system statistics and
                                    activity on {{ $formatted_startDate }} to {{ $formatted_endDate }}</div>

                            </div>

                            <div class="border-bottom pb-1">

                                @if(in_array('storage_usage', $contentOptions))
                                <div class="d-flex flex-column mb-10">
                                    <div class="fw-semibold fs-3 mb-5 text-gray-90000 border-bottom border-gray-300">
                                        Storage usage</div>
                                    <div class="py-4">
                                        <span class="fw-bolder fs-6 text-gray-900">
                                            {{ number_format($totalCurrentStorage, 2) }} MB of
                                            {{ number_format($totalSpace, 2) }} MB
                                        </span>
                                        @php
                                        // Calculate percentage of used storage
                                        $totalSpaceMB = $totalSpace; // Convert total space to MB
                                        $progress = ($totalCurrentStorage / $totalSpaceMB) * 100;
                                        $progress = min(max($progress, 0), 100); // Ensure progress is between 0 and 100
                                        @endphp

                                        <div class="h-15px w-100 bg-light-primary rounded">
                                            <div class="bg-primary rounded h-15px" role="progressbar"
                                                style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if(in_array('document_statistics', $contentOptions))
                                <div class="d-flex flex-column mb-10">
                                    <div class="fw-semibold fs-3 mb-5 text-gray-90000 border-bottom border-gray-300">
                                        Document Statistics</div>

                                    <div class="py-10">
                                        <div class="fv-row row">
                                            <div class="col-lg-6">
                                                <div class="row g-3 g-lg-6 ">
                                                    <div class="col-4">
                                                        <!--begin::Items-->
                                                        <div
                                                            class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 h-100">
                                                            <!--begin::Symbol-->
                                                            <div class="symbol symbol-30px me-5 mb-8">
                                                                <i class="fa-solid fa-file-pdf fs-1 text-primary"></i>
                                                            </div>
                                                            <!--end::Symbol-->

                                                            <!--begin::Stats-->
                                                            <div class="m-0">
                                                                <!--begin::Number-->
                                                                <span
                                                                    class="text-gray-700 fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1">{{ $docStat_pdf }}</span>
                                                                <!--end::Number-->

                                                                <!--begin::Desc-->
                                                                <span class="text-gray-500 fw-semibold fs-6">PDF</span>
                                                                <!--end::Desc-->
                                                            </div>
                                                            <!--end::Stats-->
                                                        </div>
                                                        <!--end::Items-->
                                                    </div>
                                                    <div class="col-4">
                                                        <!--begin::Items-->
                                                        <div
                                                            class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 h-100">
                                                            <!--begin::Symbol-->
                                                            <div class="symbol symbol-30px me-5 mb-8">
                                                                <span class="symbol-label">
                                                                    <i class="fas fa-file-word fs-1 text-primary"></i>
                                                                </span>
                                                            </div>
                                                            <!--end::Symbol-->

                                                            <!--begin::Stats-->
                                                            <div class="m-0">
                                                                <!--begin::Number-->
                                                                <span
                                                                    class="text-gray-700 fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1">{{ $docStat_docx }}</span>
                                                                <!--end::Number-->

                                                                <!--begin::Desc-->
                                                                <span
                                                                    class="text-gray-500 fw-semibold fs-6">docx/doc</span>
                                                                <!--end::Desc-->
                                                            </div>
                                                            <!--end::Stats-->
                                                        </div>
                                                        <!--end::Items-->
                                                    </div>
                                                    <div class="col-4">
                                                        <!--begin::Items-->
                                                        <div
                                                            class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 h-100">
                                                            <!--begin::Symbol-->
                                                            <div class="symbol symbol-30px me-5 mb-8">
                                                                <span class="symbol-label">
                                                                    <i class="fas fa-file-excel fs-1 text-primary"></i>
                                                                </span>
                                                            </div>
                                                            <!--end::Symbol-->

                                                            <!--begin::Stats-->
                                                            <div class="m-0">
                                                                <!--begin::Number-->
                                                                <span
                                                                    class="text-gray-700 fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1">{{ $docStat_excel }}</span>
                                                                <!--end::Number-->

                                                                <!--begin::Desc-->
                                                                <span
                                                                    class="text-gray-500 fw-semibold fs-6">xlsx/csv</span>
                                                                <!--end::Desc-->
                                                            </div>
                                                            <!--end::Stats-->
                                                        </div>
                                                        <!--end::Items-->
                                                    </div>
                                                    <div class="col-4">
                                                        <!--begin::Items-->
                                                        <div
                                                            class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 h-100">
                                                            <!--begin::Symbol-->
                                                            <div class="symbol symbol-30px me-5 mb-8">
                                                                <span class="symbol-label">
                                                                    <i
                                                                        class="fas fa-file-powerpoint fs-1 text-primary"></i>
                                                                </span>
                                                            </div>
                                                            <!--end::Symbol-->

                                                            <!--begin::Stats-->
                                                            <div class="m-0">
                                                                <!--begin::Number-->
                                                                <span
                                                                    class="text-gray-700 fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1">{{ $docStat_pptx }}</span>
                                                                <!--end::Number-->

                                                                <!--begin::Desc-->
                                                                <span class="text-gray-500 fw-semibold fs-6">pptx</span>
                                                                <!--end::Desc-->
                                                            </div>
                                                            <!--end::Stats-->
                                                        </div>
                                                        <!--end::Items-->
                                                    </div>
                                                    <div class="col-4">
                                                        <!--begin::Items-->
                                                        <div
                                                            class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 h-100">
                                                            <!--begin::Symbol-->
                                                            <div class="symbol symbol-30px me-5 mb-8">
                                                                <span class="symbol-label">
                                                                    <i
                                                                        class="fa-solid fa-file-image fs-1 text-primary"></i>
                                                                </span>
                                                            </div>
                                                            <!--end::Symbol-->

                                                            <!--begin::Stats-->
                                                            <div class="m-0">
                                                                <!--begin::Number-->
                                                                <span
                                                                    class="text-gray-700 fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1">{{ $docStat_images }}</span>
                                                                <!--end::Number-->

                                                                <!--begin::Desc-->
                                                                <span
                                                                    class="text-gray-500 fw-semibold fs-6">png/jpeg</span>
                                                                <!--end::Desc-->
                                                            </div>
                                                            <!--end::Stats-->
                                                        </div>
                                                        <!--end::Items-->
                                                    </div>
                                                    <div class="col-4">
                                                        <!--begin::Items-->
                                                        <div class="rounded-2 px-6 py-5 h-100"
                                                            style="background-color: #BFDDE3">
                                                            <!--begin::Symbol-->
                                                            <div class="symbol symbol-30px me-5 mb-8">
                                                                <span class="symbol-label">
                                                                    <i
                                                                        class="ki-duotone ki-element-plus fs-1 text-primary">
                                                                        <span class="path1"></span>
                                                                        <span class="path2"></span>
                                                                        <span class="path3"></span>
                                                                        <span class="path4"></span>
                                                                        <span class="path5"></span>
                                                                    </i>
                                                                </span>
                                                            </div>
                                                            <!--end::Symbol-->

                                                            <!--begin::Stats-->
                                                            <div class="m-0">
                                                                <!--begin::Number-->
                                                                <span
                                                                    class="fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1">{{ $docStat_total }}</span>
                                                                <!--end::Number-->

                                                                <!--begin::Desc-->
                                                                <span class=" fw-semibold fs-6">Total Files</span>
                                                                <!--end::Desc-->
                                                            </div>
                                                            <!--end::Stats-->
                                                        </div>
                                                        <!--end::Items-->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                {{-- <div id="kt_amcharts_3" style="height: 300px;"></div>  --}}
                                                <div id="kt_apexcharts_1" style="height: 200px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="page-break"></div>
                                @if(in_array('user_login', $contentOptions))
                                <div class="d-flex flex-column mb-10">
                                    <div class="fw-semibold fs-3 mb-5 text-gray-90000 border-bottom border-gray-300">
                                        User Login</div>
                                    <div class="table-responsive">
                                        <table class="table table-rounded table-striped border gy-7 gs-7">
                                            <thead>
                                                <tr
                                                    class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                                    <th>User</th>
                                                    <th>Access</th>
                                                    <th>Actions</th>
                                                    <th>IP Address</th>
                                                    <th>Timestamps</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @if(!empty($user_login) && count($user_login) > 0)
                                                @foreach ($user_login as $item)
                                                <tr>
                                                    <td>{{ $item->full_name }}</td>
                                                    <td>{{ $item->role_name }}</td>
                                                    <td>{{ $item->action }}</td>
                                                    <td>{{ $item->ip_address }}</td>
                                                    <td>{{ $item->created_at }}</td>
                                                </tr>
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td colspan="5" class="text-center">No records available.</td>
                                                </tr>
                                                @endif



                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif

                                @if(in_array('document_access', $contentOptions))
                                <div class="d-flex flex-column mb-10">
                                    <div class="fw-semibold fs-3 mb-5 text-gray-90000 border-bottom border-gray-300">
                                        Document Access and Modifications</div>
                                    <div class="table-responsive">
                                        <table class="table table-rounded table-striped border gy-7 gs-7">
                                            <thead>
                                                <tr
                                                    class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                                    <th>User</th>
                                                    <th>Access</th>
                                                    <th>Model</th>
                                                    <th>Actions</th>
                                                    <th>Changes</th>
                                                    <th>IP Address</th>
                                                    <th>Timestamps</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(!empty($doc_access) && count($doc_access) > 0)
                                                @foreach ($doc_access as $item)
                                                <tr>
                                                    <td>{{ $item->full_name }}</td>
                                                    <td>{{ $item->role_name }}</td>
                                                    <td >{{$item->model}}</td>
                                                    <td >{{$item->action }}</td>
                                                    <td>{{ $item->changes }}</td>
                                                    <td>{{ $item->ip_address }}</td>
                                                    <td>{{ $item->created_at }}</td>
                                                </tr>
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td colspan="7" class="text-center">No records available.</td>
                                                </tr>
                                                @endif

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif

                                @if(empty($contentOptions))
                                <div class="d-flex flex-column flex-center">
                                    <img src="{{ asset('assets/media/illustrations/sketchy-1/4.png') }}"
                                        class="mw-300px" alt="No results found illustration">
                                    <div class="fs-1 fw-bolder text-dark">No content found.</div>
                                    <div class="fs-6">Please select the contents!</div>
                                </div>
                                @endif

                            </div>
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
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>

<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="{{ asset('assets/plugins/global/plugins.bundle.js')}}"></script>
<script src="{{ asset('assets/js/scripts.bundle.js')}}"></script>
<script>
    var element = document.getElementById('kt_apexcharts_1');

    // If the element doesn't exist, do nothing
    if (!element) {
        console.error("Element with ID 'kt_apexcharts_1' not found.");
    } else {
        var options = {
            series: [
                {{ $docStat_pdf }},
                {{ $docStat_docx }},
                {{ $docStat_excel }},
                {{ $docStat_pptx }},
                {{ $docStat_images }}
            ],
            chart: {
                type: 'polarArea', // Pie chart type
                toolbar: {
                    show: false
                },

            },
            stroke: {
                colors: ['#fff']
            },
            fill: {
                opacity: 0.8
            },
            yaxis: {
                show: false
            },
            plotOptions: {
                polarArea: {
                    rings: {
                        strokeWidth: 0
                    },
                    spokes: {
                        strokeWidth: 0
                    },
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            labels: ['PDF', 'docx/doc', 'xlsx/csv', 'pptx', 'png/jpeg'], // Labels for the categories
            colors: ['#67b6dd', '#6695dc', '#6771dc', '#8166dc', '#a366dd'], // Color array for segments
            dataLabels: {
                enabled: true // Enable data labels
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + ' documents'; // Tooltip format
                    }
                }
            },
            legend: {
                position: 'right', // Position legend
            }
        };

        var chart = new ApexCharts(element, options);
        chart.render();
    }

</script>

<script>
    $(document).ready(function () {
        $('#print-btn').on('click', function () {
            printCard();
        });
    });

    function printCard() {
        // Hide the print button
        var printButton = document.querySelector('#print-btn');
        printButton.style.display = 'none';

        // Get the content to print
        var printContents = document.getElementById('printable-content').innerHTML;

        // Wrap the contents in a div and add the page break style
        var originalContents = document.body.innerHTML;

        // Add CSS for print styles, including page breaks
        var pageBreakStyle = `
            <style>
                @media print {
                 body {
                    background-color: white;
                    color: #333; 
                    margin: 0; 
                    padding: 10px; 
                }
                    .page-break {
                        page-break-before: always; /* Start a new page before this element */
                        
                    }
                }
            </style>
        `;

        // Combine the content with the style
        document.body.innerHTML = pageBreakStyle + printContents;

        // Print the content
        window.print();

        // Restore the original content and show the print button
        document.body.innerHTML = originalContents;
        printButton.style.display = 'block';
    }
</script>



@endsection
