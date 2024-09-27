@extends('admin.user.view-user')

@section('user_content')

<div class="card mb-5 mb-xl-10">
    <!--begin::Card header-->
    <div class="card-header border-0 cursor-pointer">
        <!--begin::Card title-->
        <div class="card-title m-0">
            <h3 class="fw-bold m-0">Profile Details</h3>
        </div>
        <!--end::Card title-->
         <!--end::Card title-->
         <div class="card-toolbar">
            <button class="btn btn-sm btn-danger" data-uuid="{{ $data->uuid }}" data-kt-filter="deactivated">Deactivate</button>
        </div>
    </div>
    <!--begin::Card header-->
    <!--begin::Content-->
    <div id="kt_account_settings_profile_details" class="collapse show">

        <!--begin::Form-->
        <form id="kt_account_profile_details_form" action="{{ route('userSetting.post', $data->id) }}" method="POST"
            enctype="multipart/form-data" class="form">
            @csrf
            <!--begin::Card body-->
            <div class="card-body border-top p-9">
                <!--begin::Input group-->
                <div class="row mb-6">
                    <!--begin::Label-->
                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">Full
                        Name</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8">
                        <!--begin::Row-->
                        <div class="row">
                            <!--begin::Col-->
                            <div class="col-lg-12 fv-row">
                                <input type="text" name="full_name"
                                    class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
                                    placeholder="fullname" value="{{ $data->full_name }}" />
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->
                    </div>
                    <!--end::Col-->
                </div>
                <div class="row mb-6">
                    <!--begin::Label-->
                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">IC Number</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8">
                        <!--begin::Row-->
                        <div class="row">
                            <!--begin::Col-->
                            <div class="col-lg-12 fv-row">
                                <input type="text" name="ic_number"
                                    class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
                                    placeholder="11234501293" value="{{ $data->ic_number }}" />
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Input group-->
                <div class="row mb-6">
                    <!--begin::Label-->
                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">Email</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8 fv-row">
                        <input type="email" name="email" class="form-control form-control-lg form-control-solid"
                            placeholder="email" value="{{ $data->email }}" />
                    </div>
                    <!--end::Col-->
                </div>
                <!--begin::Input group-->
                <div class="row mb-6">
                    <!--begin::Label-->
                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">Company</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8 fv-row">
                        <select class="form-select form-control form-select-solid" data-control="select2"
                            name="org_name" id="org_name" data-placeholder="Select a company" required>
                            @foreach ($org as $company )
                            <option value="{{ $company->id }}" {{  $company->id == $data->org_guid ? 'selected' : '' }}>
                                {{ $company->org_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!--end::Col-->
                </div>
                <!--end::Input group-->
                <div class="row mb-6">
                    <!--begin::Label-->
                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">Race</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8 fv-row">
                        <input type="text" name="race" class="form-control form-control-lg form-control-solid"
                            placeholder="race" value="{{ $data->race }}" />
                    </div>
                    <!--end::Col-->
                </div>
                <div class="row mb-6">
                    <!--begin::Label-->
                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">Nationality</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8 fv-row">
                        <input type="text" name="nationality" class="form-control form-control-lg form-control-solid"
                            placeholder="nationality" value="{{ $data->nationality }}" />
                    </div>
                    <!--end::Col-->
                </div>

            </div>
            <!--end::Card body-->
            <!--begin::Actions-->
            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <button type="reset" class="btn btn-light btn-active-light-primary me-2">Discard</button>
                <button type="submit" class="btn btn-primary" id="kt_account_profile_details_submit">Save
                    Changes</button>
            </div>
            <!--end::Actions-->
        </form>
        <!--end::Form-->
    </div>
    <!--end::Content-->
</div>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>

<script src="{{ asset('assets/plugins/global/plugins.bundle.js')}}"></script>
<script src="{{ asset('assets/js/scripts.bundle.js')}}"></script>
<script>
    $(document).ready(function () {
        // Handle delete button click
        $(document).on('click', '[data-kt-filter="deactivated"]', function (e) {
            e.preventDefault();

            var user_uuid = $(this).data('uuid');
            
            // SweetAlert confirmation dialog
            Swal.fire({
                text: "Are you sure you want to deactivate this user?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Yes, deactivate!",
                cancelButtonText: "No, cancel",
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then((result) => {

                if (result.value) {

                    // Send the delete request to the server
                    $.ajax({
                        url: `/admin/user-setting-deactivate/` + user_uuid,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            // Show success message
                            // console.log(response);
                            Swal.fire({
                                text: "You have deactivated this user!",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            }).then(function () {
                                    window.location.href = "/admin/user-list";
                            });

                        },
                        error: function (xhr) {
                            // Handle the error
                            Swal.fire({
                                text: "There was an error deactivate this user. Please try again.",
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
                        text: "The user was not deactivate.",
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
