<div class="flex-column flex-lg-row-auto w-100 w-xl-350px mb-10">
    <!--begin::Card-->
    <div class="card mb-5 mb-xl-8">
       
        <div class="card-body pt-15">
            <!--begin::Summary-->
            <div class="d-flex flex-center flex-column mb-5">
                <!--begin::Avatar-->
                <div class="symbol symbol-100px symbol-circle mb-7">
                    <i class="fa-solid fa-building fs-5x" ></i>
                </div>
                <!--end::Avatar-->
                <!--begin::Name-->
                <a href="#"
                    class="fs-3 text-gray-800 text-hover-primary fw-bold mb-1">{{$data->org_name}}</a>
                <!--end::Name-->
                <!--begin::Position-->
                <div class="fs-5 fw-semibold text-muted mb-6">{{ $data->nature_of_business }}</div>
                <!--end::Position-->
                <!--begin::Info-->
                <div class="d-flex flex-wrap flex-center">
                    <!--begin::Stats-->
                    <div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3">
                        <div class="fs-2 fw-bold" data-kt-countup="true"
                                        data-kt-countup-value="{{ $userCount }}">0</div>
                        <div class="fw-semibold text-muted">Users</div>
                    </div>
                    <!--end::Stats-->
                    <!--begin::Stats-->
                    <div class="border border-gray-300 border-dashed rounded py-3 px-3 mx-4 mb-3">
                        <div class="fs-2 fw-bold" data-kt-countup="true"
                                        data-kt-countup-value="{{ $folderCount }}">0</div>
                        <div class="fw-semibold text-muted">Folders</div>
                    </div>
                    <!--end::Stats-->
                    <!--begin::Stats-->
                    <div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3">
                        <div class="fs-2 fw-bold" data-kt-countup="true"
                                        data-kt-countup-value="{{ $fileCount }}">0</div>
                        <div class="fw-semibold text-muted">Files</div>
                    </div>
                    <!--end::Stats-->
                </div>
                <!--end::Info-->
            </div>
            <!--end::Summary-->
            <!--begin::Details toggle-->
            <div class="d-flex flex-stack fs-4 py-3">
                <div class="fw-bold rotate collapsible" data-bs-toggle="collapse"
                    href="#kt_customer_view_details" role="button" aria-expanded="false"
                    aria-controls="kt_customer_view_details">
                    Details
                    <span class="ms-2 rotate-180">
                        <i class="ki-duotone ki-down fs-3"></i>
                    </span></div>
            </div>
            <!--end::Details toggle-->
            <div class="separator separator-dashed my-3"></div>
            <!--begin::Details content-->
            <div id="kt_customer_view_details" class="collapse show">
                <div class="py-5 fs-6">
                    <!--begin::Details item-->
                    <div class="fw-bold mt-5">Register Numbers</div>
                    <div class="text-gray-600">{{ $data->org_number }}</div>
                    <!--begin::Details item-->
                    <!--begin::Details item-->
                    <div class="fw-bold mt-5">Register Date</div>
                    <div class="text-gray-600">
                        <a href="#"
                            class="text-gray-600 text-hover-primary">{{ $data->reg_date }}</a>
                    </div>
                    <!--begin::Details item-->
                    <!--begin::Details item-->
                    <div class="fw-bold mt-5">Address</div>
                    <div class="text-gray-600">{{ $data->org_address }}</div>
                    <!--begin::Details item-->
                    <!--begin::Details item-->
                    <div class="fw-bold mt-5">State</div>
                    <div class="text-gray-600">{{ $data->org_place }}</div>
                    <!--begin::Details item-->
                </div>
            </div>
            <!--end::Details content-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
</div>