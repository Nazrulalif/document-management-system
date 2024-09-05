<div class="modal fade" id="kt_modal_edit_company" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header" id="kt_modal_add_user_header">
                <!--begin::Modal title-->
                <h2 class="fw-bold">Edit Company</h2>
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body px-5 my-7">
                <!--begin::Form-->
                <form id="kt_modal_edit_company_form" class="form" action="" method="post"
                    autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    
                    <!--begin::Scroll-->
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add_user_scroll"
                        data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto"
                        data-kt-scroll-dependencies="#kt_modal_add_user_header"
                        data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">
                        <!--begin::Input group-->
                        <input type="hidden" id="editId" name="id">
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">Company Name</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" name="org_name" id="org_name" class="form-control form-control-solid mb-3 mb-lg-0"
                                placeholder="Full Company Name"   required />
                            <!--end::Input-->
                            @error('org_name')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row mb-7">
                            <div class="col-md-6">
                                <!--begin::Label-->
                                <label class="required fw-semibold fs-6 mb-2">Register Date</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="date" name="reg_date" id="reg_date" class="form-control form-control-solid mb-3 mb-lg-0"
                                    placeholder="Register date" required />
                                <!--end::Input-->
                                @error('reg_date')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <!--begin::Label-->
                                <label class="required fw-semibold fs-6 mb-2">Register Number</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="org_number" id="org_number"
                                    class="form-control form-control-solid mb-3 mb-lg-0"
                                    placeholder="Company Register Number" required />
                                <!--end::Input-->
                                @error('org_number')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6 mb-2">Address</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" name="org_address" id="org_address" class="form-control form-control-solid mb-3 mb-lg-0"
                                placeholder="Company Address" required />
                            <!--end::Input-->
                            @error('org_address')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Input group-->
                        <div class="row mb-7">
                            <div class="col-md-6">
                                <!--begin::Label-->
                                <label class="required fw-semibold fs-6 mb-2">State</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="org_place" id="org_place" class="form-control form-control-solid mb-3 mb-lg-0"
                                    placeholder="State" required />
                                <!--end::Input-->
                                @error('org_place')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <!--begin::Label-->
                                <label class="required fw-semibold fs-6 mb-2">Nature of Business</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="nature_of_business" id="nature_of_business"
                                    class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Food and Beverage"
                                    required />
                                <!--end::Input-->
                                @error('nature_of_bussiness')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!--end::Scroll-->
                    <!--begin::Actions-->
                    <div class="text-center pt-10">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Discard</button>
                        <button type="submit" class="btn btn-primary" id="kt_button_submit">
                            <span class="indicator-label">Submit</span>
                            <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                    <!--end::Actions-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>


