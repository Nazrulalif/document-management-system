<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex ">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 align-items-center my-0">
                    @php
                    function formatPath($path) {
                        // Split the path into segments
                        $segments = explode('/', $path);
                    
                        // If there's more than one segment, remove the last one
                        if (count($segments) >= 2) {
                            array_pop($segments);
                        }
                    
                        // Join the remaining segments and replace dashes with spaces
                        $formattedPath = str_replace(['-', '*'], ' ', implode(' ', $segments));
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
                        <a href="{{ route('home.user') }}" class="text-muted text-hover-primary">
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
            <!--end::Toolbar container-->
        </div>