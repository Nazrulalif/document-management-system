<div class="modal fade" id="kt_modal_view_version" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header" id="kt_modal_add_user_header">
                <!--begin::Modal title-->
                <h2 class="fw-bold" id="modalVersionTitle"></h2>
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

                <div class="d-flex flex-column scroll-y px-5 px-lg-10" data-kt-scroll="true"
                    data-kt-scroll-activate="true" data-kt-scroll-max-height="auto" data-kt-scroll-offset="300px">

                    <div class="fv-row mb-7">
                        <h4 class="text-gray-700 fw-bold">Change Description</h4>
                        <div id="modalVersionDescription" class="text-gray-600 fw-semibold fs-6"></div>
                    </div>
                    <div class="fv-row mb-7">
                        <h4 class="text-gray-700 fw-bold">Created by</h4>
                        <div id="modalCreatedBy" class="text-gray-600 fw-semibold fs-6"></div>
                    </div>
                    <div class="fv-row mb-7">
                        <h4 class="text-gray-700 fw-bold">Files</h4>
                        <a href="/" target="_blank" class="btn btn-primary" id="modalfile" style="display: flow-root">
                            <i class="ki-duotone ki-fasten fs-2x"><span class="path1"></span><span
                                    class="path2"></span></i>
                            <span class="fw-bold">Download</span>
                        </a>
                    </div>

                </div>

            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
