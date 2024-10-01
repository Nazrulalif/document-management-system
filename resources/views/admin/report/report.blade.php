@extends('layouts.user_type.auth')

@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid py-3 py-lg-8">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-fluid">

                <div class="card card-flush">
                    <!--begin::Card header-->
                    <div class="card-header pt-8">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <h2>Generate Report</h2>
                        </div>
                        <!--end::Card title-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body">
                        <!--begin::Form-->
                        <form class="form" action="{{ route('report.post') }}" method="POST"
                            enctype="multipart/form-data" id="kt_file_manager_settings" autocomplete="off">
                            @csrf
                            <!--begin::Input group-->
                            <div class="fv-row row mb-15">
                                <!--begin::Col-->
                                <div class="col-md-3">
                                    <!--begin::Label-->
                                    <label class="required fs-6 fw-semibold mt-2">Date</label>
                                    <!--end::Label-->
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-9">
                                    <!--begin::Input-->
                                    <input class="form-control form-control-solid" placeholder="Pick start and end date"
                                        id="kt_daterangepicker_1" name="date" required />
                                    <!--end::Input-->

                                    <!-- Hidden fields to store the start and end dates for form submission -->
                                    <input type="hidden" id="start_date" name="start_date" readonly>
                                    <input type="hidden" id="end_date" name="end_date" readonly>
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            @if (Auth::user()->role_guid == 1)
                            <div class="fv-row row mb-15">
                                <!--begin::Col-->
                                <div class="col-md-3 d-flex align-items-center">
                                    <!--begin::Label-->
                                    <label class="required fs-6 fw-semibold">Company</label>
                                    <!--end::Label-->
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-9">
                                   
                                    <div class="form-check form-switch form-check-custom form-check-solid me-10 mb-5">
                                        <input class="form-check-input h-30px w-50px" name="all_company" type="checkbox" value="1" id="all_company" checked />
                                        <label class="form-check-label fw-semibold text-muted" for="all_company">All Companies</label>
                                    </div>
                                    
                                    <!--begin::Input-->
                                    <div id="company_selection_container" style="display: none;">
                                        <select class="form-select form-select-solid" id="org_select" data-control="select2"
                                            data-close-on-select="true" data-placeholder="Select company..."
                                            data-allow-clear="true" multiple="multiple" name="org_name[]" >
                                            @foreach ($organization as $item)
                                            <option value="{{ $item->id }}">{{ $item->org_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!--end::Input-->
                                </div>
                                <!--end::Col-->
                            </div>
                            @else
                            <input type="hidden" value="{{ Auth::user()->org_guid }}" name="org_name[]" readonly>
                            @endif
                            
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row row mb-15">
                                <!--begin::Col-->
                                <div class="col-md-3 d-flex align-items-center">
                                    <!--begin::Label-->
                                    <label class="required fs-6 fw-semibold">Content</label>
                                    <!--end::Label-->
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-9">
                                    <!--begin::Switch-->
                                    <div class="form-check form-switch form-check-custom form-check-solid me-10 mb-5">
                                        <input class="form-check-input h-30px w-50px" name="content[]" type="checkbox"
                                            value="storage_usage" id="content_1" checked />
                                        <label class="form-check-label fw-semibold text-muted" for="content_1">Storage Usage</label>
                                    </div>
                                
                                    <div class="form-check form-switch form-check-custom form-check-solid me-10 mb-5">
                                        <input class="form-check-input h-30px w-50px" name="content[]" type="checkbox"
                                            value="user_login" id="content_2" checked />
                                        <label class="form-check-label fw-semibold text-muted" for="content_2">User Login</label>
                                    </div>
                                
                                    <div class="form-check form-switch form-check-custom form-check-solid me-10 mb-5">
                                        <input class="form-check-input h-30px w-50px" name="content[]" type="checkbox"
                                            value="document_statistics" id="content_3" checked />
                                        <label class="form-check-label fw-semibold text-muted" for="content_3">Document Statistics</label>
                                    </div>
                                
                                    <div class="form-check form-switch form-check-custom form-check-solid me-10 mb-5">
                                        <input class="form-check-input h-30px w-50px" name="content[]" type="checkbox"
                                            value="document_access" id="content_4" checked />
                                        <label class="form-check-label fw-semibold text-muted" for="content_4">Document Access and Modification</label>
                                    </div>
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Action buttons-->
                            <div class="row mt-12 justify-content-end">
                                <div class="col-md-9">
                                    <!--begin::Button-->
                                    <button type="submit" class="btn btn-primary" id="kt_file_manager_settings_submit">
                                        <span class="indicator-label">Generate</span>
                                        <span class="indicator-progress">Please wait...
                                            <span
                                                class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button>
                                    <!--end::Button-->
                                </div>
                            </div>
                            <!--begin::Action buttons-->
                        </form>
                        <!--end::Form-->
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
<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="{{ asset('assets/plugins/global/plugins.bundle.js')}}"></script>

<!--end::Global Javascript Bundle-->
<script>
    // Initialize the Date Range Picker
    $(function () {
        $('#kt_daterangepicker_1').daterangepicker({
            autoUpdateInput: false, // Prevent automatic setting of the input field
            locale: {
                cancelLabel: 'Clear'
            }
        });

        // Event handler when a date range is selected
        $('#kt_daterangepicker_1').on('apply.daterangepicker', function (ev, picker) {
            // Set the selected start and end dates to the hidden input fields
            $('#start_date').val(picker.startDate.format('YYYY-MM-DD'));
            $('#end_date').val(picker.endDate.format('YYYY-MM-DD'));

            // Set the visible input field to show the selected date range
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                'DD/MM/YYYY'));
        });

        // Event handler for when the picker is canceled (cleared)
        $('#kt_daterangepicker_1').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val(''); // Clear the input field
            $('#start_date').val(''); // Clear the hidden field
            $('#end_date').val(''); // Clear the hidden field
        });
    });

    // Check initial state of the checkbox
    toggleCompanySelection();

    // Handle checkbox change event
    $('#all_company').on('change', function () {
        toggleCompanySelection();
    });

    // Function to show/hide the company selection dropdown
    function toggleCompanySelection() {
        if ($('#all_company').is(':checked')) {
            // If checkbox is checked, hide the company selection
            $('#company_selection_container').hide();
            // Clear any selected companies
            $('#org_select').val(null).trigger('change');
            $('#org_select').attr('required', false);
        } else {
            // If checkbox is unchecked, show the company selection
            $('#company_selection_container').show();
            $('#org_select').attr('required', true);
        }
    }

</script>



@endsection
