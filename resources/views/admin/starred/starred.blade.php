@extends('layouts.user_type.auth')

@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid py-3 py-lg-8">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-fluid">

                @if($starred_folder->count() > 0 || $starred_doc->count() > 0)
                @if($starred_folder->count() > 0 )
                <h6 class="fw-bold text-gray-600 my-2 pb-4">Folders</h6>
                @endif
                <div class="row g-4 g-xl-5 mb-6 mb-xl-5">
                    @foreach ($starred_folder as $item)
                    <!--begin::Col-->
                    <div class="col-md-4 col-lg-4 col-xl-3 ">
                        <!--begin::Card-->
                        <div class="card h-100">
                            <!--begin::Card body-->
                            <div class="card-body d-flex flex-wrap flex-stack p-5">
                                <!--begin::Name-->
                                <a href="{{ route('folder.show', $item->uuid) }}"
                                    class="text-gray-800 text-hover-primary d-flex align-items-center">
                                    <i class="ki-duotone ki-folder fs-2x text-primary me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <!--begin::Title-->
                                    <!--end::Title-->
                                    <div class="fs-7 fw-bold">{{ $item->folder_name }}</div>
                                </a>
                                <div class="d-flex">

                                    @if(in_array($item->id, $starredFolders))

                                    <i class="ki-duotone ki-star fs-3 star-icon text-warning cursor-pointer"
                                        data-id="{{ $item->id }}" data-type="folder"></i> <!-- Starred -->
                                    @else
                                    <i class="ki-outline ki-star fs-3 star-icon cursor-pointer"
                                        data-id="{{ $item->id }}" data-type="folder"></i> <!-- Not starred -->
                                    @endif
                                </div>
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->

                    </div>
                    @endforeach
                </div>
                @if($starred_doc->count() > 0 )
                <h6 class="fw-bold text-gray-600 my-2 pb-4">Files</h6>
                @endif
                <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                    @foreach ($starred_doc as $item)
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <!--begin::Card-->
                        <div class="card h-100">
                            <!--begin::Card body-->
                            <div class="card-body d-flex justify-content-center text-center flex-column p-8 pt-5">
                                <div class="d-flex flex-end">
                                    @if(in_array($item->id, $starredDoc))

                                    <i class="ki-duotone ki-star fs-3 star-icon text-warning cursor-pointer"
                                        data-id="{{ $item->id }}" data-type="document"></i> <!-- Starred -->
                                    @else
                                    <i class="ki-outline ki-star fs-3 star-icon cursor-pointer"
                                        data-id="{{ $item->id }}" data-type="document"></i>
                                    @endif
                                </div>
                                <!--begin::Name-->
                                <a href="{{ route('file.index', $item->latest_version_guid) }}"
                                    class="text-gray-800 text-hover-primary d-flex flex-column">
                                    <!--begin::Image-->
                                    <div class="symbol symbol-60px mb-5">
                                        @if ($item->doc_type == 'pdf')
                                        <img src="{{ asset('assets/media/icons/duotune/files/pdf-file.png') }}" />
                                        @elseif ($item->doc_type == 'docx' || $item->doc_type == 'doc')
                                        <img src="{{ asset('assets/media/icons/duotune/files/word-file.png') }}" />
                                        @elseif ($item->doc_type == 'xlsx' || $item->doc_type == 'csv')
                                        <img src="{{ asset('assets/media/icons/duotune/files/excel-file.png') }}" />
                                        @elseif ($item->doc_type == 'pptx')
                                        <img src="{{ asset('assets/media/icons/duotune/files/pptx-file.png') }}" />
                                        @elseif ($item->doc_type == 'images')
                                        <img src="{{ asset('assets/media/icons/duotune/files/image-file.png') }}" />
                                        @endif
                                    </div>
                                    <!--end::Image-->
                                    <!--begin::Title-->
                                    <div class="fs-5 fw-bold mb-2">{{ $item->doc_title }}</div>
                                    <!--end::Title-->
                                </a>
                                <!--end::Name-->
                                <!--begin::Description-->
                                <div class="fs-7 fw-semibold text-gray-500">{{ $item->created_at->format('d/m/Y') }}
                                </div>
                                <!--end::Description-->

                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    @endforeach

                </div>
                @else
                <div class="d-flex flex-column flex-center">
                    <img src="{{ asset('assets/media/illustrations/sketchy-1/4.png') }}" class="mw-300px" alt="">
                    <div class="fs-1 fw-bolder text-dark">No Starred Items</div>
                    <div class="fs-6">Add stars to things that you want to easily find later</div>
                </div>
                @endif
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->

    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js')}}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js')}}"></script>
    <script>
        $(document).on('click', '.star-icon', function () {
            const element = $(this);
            const id = element.data('id');
            const type = element.data('type');

            $.ajax({
                url: `/admin/star`, // Adjust this URL as per your route
                type: 'POST',
                data: {
                    id: id,
                    type: type,
                    _token: "{{ csrf_token() }}",
                },
                success: function (response) {
                    if (response.success) {
                        if (response.starred) {
                            // If starred, switch to the duotone style and add text-warning class
                            element.removeClass('ki-outline').addClass('ki-duotone text-warning');
                        } else {
                            // If unstarred, switch to the outline style and remove text-warning class
                            element.removeClass('ki-duotone text-warning').addClass('ki-outline');
                        }
                    }
                },
                error: function () {
                    alert('Something went wrong. Please try again.');
                }
            });
        });

    </script>
    @endsection
