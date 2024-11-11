@extends('layouts.user_type.auth')

@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        @include('layouts.appToolbar-user')

        
        @if (Auth::user()->is_change_password == 'N' && Auth::user()->login_method == 'email_password')
            @include('user.modal-change-password')
        @endif
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid py-3 py-lg-8">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-fluid">
                <h6 class="fw-bold text-gray-600 my-2 pb-4">Suggested Folders</h6>
                <div class="row g-4 g-xl-5 mb-6 mb-xl-5">
                    <!--begin::Col-->

                    @forelse ($folder as $item)
                    <div class="col-md-4 col-lg-4 col-xl-3 ">
                        <div class="card p-0">
                            <div class="card-body p-0">
                                <a href="{{ route('folder.show.user', $item->uuid) }}"
                                    class="btn h-100 w-100 text-gray-800 btn-active-secondary d-flex align-items-center p-5">
                                    <i class="ki-duotone ki-folder fs-2x text-primary me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <!--begin::Title-->
                                    <!--end::Title-->
                                    <div class="fs-7 fw-bold">{{ $item->folder_name }}</div>
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="d-flex flex-column flex-center">
                        <img src="{{ asset('assets/media/illustrations/sketchy-1/4.png') }}" class="mw-300px" alt="">
                        <div class="fs-1 fw-bolder text-dark">No Suggestion Folders</div>
                    </div>
                    @endforelse
                </div>

                <h6 class="fw-bold text-gray-600 my-2 pb-4">Suggested Files</h6>

                <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                    @forelse ($document as $item)

                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <!--begin::Card-->
                        <div class="card h-100 p-0">
                            <!--begin::Card body-->
                            <div class="card-body p-0">
                                <a href="{{ route('file.user', $item->latest_version_guid) }}"
                                    class="btn h-100 w-100 text-gray-800 btn-active-secondary d-flex flex-column p-5">
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
                                    <div class="fs-5 fw-bold mb-2">{{ $item->doc_title }}</div>
                                    <div class="fs-7 fw-semibold text-gray-500">{{ $item->created_at->format('d/m/Y') }}
                                    </div>
                                </a>
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    @empty
                    <div class="d-flex flex-column flex-center">
                        <img src="{{ asset('assets/media/illustrations/sketchy-1/4.png') }}" class="mw-300px" alt="">
                        <div class="fs-1 fw-bolder text-dark">No Suggestion Files</div>
                    </div>
                    @endforelse

                </div>

            </div>
            <!--end::Content-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->

</div>
<script src="{{ asset('assets/js/scripts.bundle.js')}}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('kt_modal_1'));
        myModal.show();
    });
</script>

@endsection
