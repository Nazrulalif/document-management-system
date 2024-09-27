@extends('admin.user.view-user')

@section('user_content')
<div class="d-flex flex-wrap flex-stack my-5">
    <!--begin::Heading-->
    <h3 class="fw-bold my-2">Files
        <span class="fs-6 text-gray-500 fw-semibold ms-1">+{{ $fileCount }}</span></h3>
    <!--end::Heading-->

    <form action="{{ route('user.file', $data->uuid) }}" method="GET" class="d-flex" autocomplete="off">
        <div class="d-flex">
            <div class="input-group mb-4">
                <input type="text" name="query" value="{{ request('query') }}" class="form-control form-control-solid"
                    placeholder="Search" />
                <button type="submit" class="btn btn-flex btn-light-primary">
                    <i class="ki-duotone ki-magnifier fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Search
                </button>
            </div>
        </div>
    </form>
</div>
<div class="row g-6 g-xl-9 mb-6 mb-xl-9">
    <!--begin::Col-->
    @forelse ($fileList as $item)
    <div class="col-md-6 col-lg-4 col-xl-3">
        <!--begin::Card-->
        <div class="card h-100">
            <!--begin::Card body-->
            <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                <!--begin::Name-->
                <a href="{{ route('file.index', $item->latest_version_guid)}}"
                    class="text-gray-800 text-hover-primary d-flex flex-column">
                    <!--begin::Image-->
                    <div class="symbol symbol-60px mb-5">
                        @if ($item->doc_type == 'pdf')
                        <img src="{{ asset('assets/media/svg/files/pdf-file.svg') }} " class="theme-light-show"
                            alt="" />
                        <img src="{{ asset('assets/media/svg/files/pdf-file.svg') }} " class="theme-dark-show" alt="" />
                        @elseif ($item->doc_type == 'docx' || $item->doc_type == 'doc' )
                        <img src="{{ asset('assets/media/svg/files/docx-file.svg') }} " class="theme-light-show"
                            alt="" />
                        <img src="{{ asset('assets/media/svg/files/docx-file.svg') }} " class="theme-dark-show"
                            alt="" />
                        @elseif ($item->doc_type == 'xlsx' || $item->doc_type == 'csv' )
                        <img src="{{ asset('assets/media/svg/files/excel-file.svg') }} " class="theme-light-show"
                            alt="" />
                        <img src="{{ asset('assets/media/svg/files/excel-file.svg') }} " class="theme-dark-show"
                            alt="" />
                        @elseif ($item->doc_type == 'images' )
                        <img src="{{ asset('assets/media/svg/files/image-file.svg') }} " class="theme-light-show"
                            alt="" />
                        <img src="{{ asset('assets/media/svg/files/image-file.svg') }} " class="theme-dark-show"
                            alt="" />
                        @endif

                    </div>
                    <!--end::Image-->
                    <!--begin::Title-->
                    <div class="fs-5 fw-bold mb-2">{{ $item->doc_title }}</div>
                    <!--end::Title-->
                </a>
                <!--end::Name-->
                <!--begin::Description-->
                <div class="fs-7 fw-semibold text-gray-500">{{ $item->created_at->format('d/m/Y') }}</div>
                <!--end::Description-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    @empty
    <div class="d-flex flex-column flex-center">
        <img src="{{ asset('assets/media/illustrations/sketchy-1/4.png') }}" class="mw-300px" alt="">
        <div class="fs-1 fw-bolder text-dark">No files found.</div>
        <div class="fs-6">Start upload your files!</div>
    </div>
    @endforelse

    <!--end::Col-->
    <div class="d-flex paginations flex-center">
        {{ $fileList->links('pagination::bootstrap-4') }}
    </div>

</div>
@endsection