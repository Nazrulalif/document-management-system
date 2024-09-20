@extends('layouts.user_type.auth')

@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid py-3 py-lg-8">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="d-flex flex-column flex-lg-row">
                    <!--begin::Aside-->
                    <div class="flex-column flex-lg-row-auto w-100 w-lg-250px w-xxl-325px mb-8 mb-lg-0 me-lg-9 me-5">
                        <!--begin::Form-->
                        <form action="{{ route('search.index') }}" method="get" enctype="multipart/form-data" id="filterForm">
                            <!-- Retain existing query parameters -->
                            @foreach(request()->except(['type']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            
                            <!--begin::Card-->
                            <div class="card">
                                <!--begin::Body-->
                                <div class="card-body">
                                    <h3 class="fw-bold my-1">Filter By</h3>
                                    <!--begin::Border-->
                                    <div class="separator separator-dashed my-8"></div>
                                    <!--end::Border-->
                                    
                                    <!--begin::Input group-->
                                    <div class="mb-5">
                                        <label class="fs-6 form-label fw-bold text-gray-900 mb-5">Type</label>
                                        <div class="form-check form-check-custom form-check-solid form-check-sm mb-3">
                                            <input class="form-check-input" type="radio" value="folder" name="type" id="flexRadioLg" 
                                            {{ request('type') === 'folder' ? 'checked' : 'checked' }} 
                                            onchange="document.getElementById('filterForm').submit();" />
                                            <label class="form-check-label flex-grow-1 fw-semibold text-gray-700 fs-6" for="flexRadioLg">
                                                Folder
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                                            <input class="form-check-input" type="radio" value="file" name="type" id="flexRadioLg2" 
                                            {{ request('type') === 'file' ? 'checked' : '' }} 
                                            onchange="document.getElementById('filterForm').submit();" />
                                            <label class="form-check-label flex-grow-1 fw-semibold text-gray-700 fs-6" for="flexRadioLg2">
                                                File
                                            </label>
                                        </div>
                                    </div>
{{-- 
                                    <div class="mb-5" id="fileTypeContainer" style="display: {{ request('type') === 'file' ? 'block' : 'none' }};">
                                        <label class="fs-6 form-label fw-bold text-gray-900">File type</label>
                                        <!--begin::Select-->
                                        <select class="form-select form-select-solid" name="file_type">
                                            <option value="">All</option>
                                            <option value="pdf" {{ request('file_type') === 'pdf' ? 'selected' : '' }}>PDF</option>
                                            <option value="xlsx" {{ request('file_type') === 'xlsx' ? 'selected' : '' }}>XLSX</option>
                                            <option value="ppt" {{ request('file_type') === 'ppt' ? 'selected' : '' }}>PPT</option>
                                            <option value="docx" {{ request('file_type') === 'docx' ? 'selected' : '' }}>DOCX</option>
                                        </select>
                                        <!--end::Select-->
                                    </div> --}}
                                </div>
                                <!--end::Body-->
                            </div>
                            <!--end::Card-->
                        </form>
                        
                        
                        <!--end::Form-->
                    </div>
                    <!--end::Aside-->

                    <!--begin::Layout-->
                    <div class="flex-lg-row-fluid">
                        <!--begin:Search-->
                        <form action="{{ route('search.index') }}" method="get" enctype="multipart/form-data"
                            autocomplete="off">

                            <div class="d-flex">

                                <div class="input-group mb-4">
                                    <input type="text" id="kt_filter_search" name="query" value="{{ request('query') }}"
                                        class="form-control form-control-solid" name="search" placeholder="Search" />

                                    <button type="submit" class="btn btn-flex btn-light-secondary">
                                        <i class="ki-duotone ki-magnifier fs-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Search
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" value="{{ request('type') ? request('type') : 'folder' }}" name="type">

                        </form>
                        <!--end:Search-->
                        <!--begin::Tab Content-->
                        <div class="tab-content">
                            <!--begin::Tab pane-->
                            @if(isset($query) && !empty($query))
                                <!--begin::Results container-->
                                <div class="fv-row mb-4 ">
                                    {{-- <h5>{{ $results['totalResult'] }} Results</h5> --}}
                                </div>
                                <div class="fv-row">
                                    <!-- Display folder results if available -->
                                    @if($results->has('folder') && $results->get('folder')->isNotEmpty())
                                        @foreach($results['folder'] as $result)
                                        <div class="fv-row mb-3">
                                            <div class="card">
                                                <div class="card-body p-5">
                                                    <div class="d-flex align-items-center">
                                                        <span class="icon-wrapper">
                                                            <i class="ki-duotone ki-folder fs-2x text-primary me-4">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                        </span>
                                                        <a href="{{ route('folder.show', $result->uuid) }}"
                                                            class="mb-1 text-gray-800 text-hover-primary">{!! $result->highlighted_title !!}</a>
                                                    </div>
                        
                                                    <div class="fv-row mt-3">
                                                        <div class="fs-7 text-gray-600">
                                                            <div class="d-flex align-items-center">
                                                                <div class="symbol symbol-50px me-2">
                                                                    <i class="ki-outline ki-profile-circle fs-1">
                                                                        <span class="path2"></span>
                                                                        <span class="path3"></span>
                                                                    </i>
                                                                </div>
                                                                {{ $result->full_name }} | {{ $result->org_name }} |
                                                                {{ $result->created_at->format('d/m/Y') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                         
                                        @endforeach
                        
                                    @endif
                        
                                    <!-- Display document results if available -->
                                    @if($results->has('document') && $results->get('document')->isNotEmpty())
                                        @foreach($results['document'] as $result)
                                        <div class="fv-row mb-3">
                                            <div class="card">
                                                <div class="card-body p-5">
                                                    <div class="d-flex align-items-center">
                                                        <span class="icon-wrapper">
                                                            @if ($result->doc_type == 'pdf')
                                                                <i class="fa-solid fa-file-pdf fs-2x me-4" style="color:red"></i>
                                                            @elseif ($result->doc_type == 'docx' || $result->doc_type == 'doc')
                                                                <i class="fas fa-file-word text-primary fs-2x me-4"></i>
                                                            @elseif ($result->doc_type == 'xlsx' || $result->doc_type == 'csv')
                                                                <i class="fas fa-file-excel fs-2x me-4" style="color:green"></i>
                                                            @elseif ($result->doc_type == 'pptx')
                                                                <i class="fa-solid fa-file-powerpoint fs-2x me-4" style="color: orange"></i>
                                                            @elseif ($result->doc_type == 'images')
                                                                <i class="fa-solid fa-image text-gray-800 fs-2x me-4"></i>
                                                            @endif
                                                        </span>
                                                        <a href="{{ route('file.index', $result->latest_version_guid) }}"
                                                            class="text-gray-800 text-hover-primary">{!! $result->highlighted_title !!}</a>
                                                    </div>
                        
                                                    <div class="fv-row mt-3">
                                                        <div class="fs-7 my-2 text-gray-600">
                                                            {!! $result->highlighted_content !!}
                                                        </div>
                                                        <div class="fs-7 text-gray-600">
                                                            <div class="d-flex align-items-center">
                                                                <div class="symbol symbol-50px me-2">
                                                                    <i class="ki-outline ki-profile-circle fs-1">
                                                                        <span class="path2"></span>
                                                                        <span class="path3"></span>
                                                                    </i>
                                                                </div>
                                                                {{ $result->full_name }} | {{ $result->org_name }} |
                                                                {{ $result->created_at->format('d/m/Y') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                        
                                        <!-- Pagination links for documents -->
                                        <div class="d-flex justify-content-center mt-4">
                                            {{ $results['document']->links('pagination::bootstrap-4') }}
                                        </div>
                                    @endif
                        
                                    <!-- If no folders or documents found, show a message -->
                                    @if((!isset($results['folder']) || $results['folder']->isEmpty()) &&
                                    (!isset($results['document']) || $results['document']->isEmpty()))
                                    <div class="d-flex flex-column flex-center">
                                        <img src="{{ asset('assets/media/illustrations/sketchy-1/5.png') }}"
                                            class="mw-300px" alt="">
                                        <div class="fs-1 fw-bolder text-dark">No items found.</div>
                                        <div class="fs-6">Please enter the correct keyword!</div>
                                    </div>
                                    @endif
                                </div>
                            @else
                                <!-- If query is empty, show a message to enter a keyword -->
                                <div class="d-flex flex-column flex-center">
                                    <img src="{{ asset('assets/media/illustrations/sketchy-1/5.png') }}" class="mw-300px"
                                        alt="">
                                    <div class="fs-1 fw-bolder text-dark">Enter keyword to Search</div>
                                    <div class="fs-6">Start searching your folders or files!</div>
                                </div>
                            @endif
                        </div>
                        

                        <!--end::Layout-->
                    </div>
                </div>

                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->


    </div>


    @endsection
