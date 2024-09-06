<div data-kt-swapper="true" data-kt-swapper-mode="{default: 'prepend', lg: 'prepend'}"
    data-kt-swapper-parent="{default: '#kt_app_content_container', lg: '#kt_app_header_wrapper'}"
    class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">

    <!--begin::Title-->
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 align-items-center my-0">
        @php
        function formatPath($path) {
            // Split the path into segments
            $segments = explode('/', $path);
        
            // If there's more than one segment, remove the last one
            if (count($segments) > 2) {
                array_pop($segments);
            }
        
            // Join the remaining segments and replace dashes with spaces
            $formattedPath = str_replace(['admin','-', '*'], ' ', implode(' ', $segments));
            return ucwords(strtolower($formattedPath));
        }
        @endphp
        
        {{ formatPath(Request::path()) }}
    </h1>
    <!--end::Title-->

    <!--begin::Separator-->
    <span class="h-20px border-gray-300 border-start mx-4"></span>
    <!--end::Separator-->

    <!--begin::Breadcrumb-->
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 ">
        <!--begin::Item-->
        <li class="breadcrumb-item text-muted">
            <a href="/metronic8/demo1/index.html" class="text-muted text-hover-primary">
                Home </a>
        </li>
        <!--end::Item-->
        <!--begin::Item-->
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <!--end::Item-->

        <!--begin::Item-->
        <li class="breadcrumb-item text-muted">
            {{ formatPath(Request::path()) }}
        </li>
        <!--end::Item-->

    </ul>
    <!--end::Breadcrumb-->
</div>
