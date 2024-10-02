<div>
    <form action="{{ route('advance-search.user') }}"
        class="w-100 w-lg-600px w-xl-800px position-relative align-items-center my-0" autocomplete="off" method="GET">

        <input class="form-control form-control-lg form-control-solid " type="text" name="query" placeholder="Search"
            value="{{ request('query') }}" wire:model.live="query">
        <!-- Search icon as a button -->
        <button type="submit"
            class="btn btn-light-secondary position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent">
            <i class="ki-duotone ki-magnifier fs-2 fs-lg-1 text-gray-500">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </button>

        @if(strlen($query) > 0)
        <div class="dropdown-menu w-100 p-7 d-block">
            <!--begin::Items-->
            <div class="scroll-y mh-200px mh-lg-350px">
                <a href="{{ route('advance-search.user', ['query' => $query]) }}"
                    class="d-flex flex-stack pb-3 d-flex text-gray-900 text-hover-primary">
                    <span class="fs-6 fw-semibold">{{ $query }}</span>
                    <span class="fs-6 fw-normal">Search all on advance search</span>
                </a>


                @if(count($folderResults) > 0)
                <div class="separator mb-4"></div>
                <!--begin::Category title-->
                <h3 class="fs-5 text-muted m-0 pb-5" data-kt-search-element="category-title">Folders</h3>
                <!--end::Category title-->
                <!--begin::Item-->
                @foreach($folderResults as $folder)
                <a href="{{ route('folder.show.user', $folder->uuid) }}" class="d-flex text-gray-900 text-hover-primary align-items-center mb-5">
                    <!--begin::Symbol-->
                    <div class="icon-wrapper">
                        <i class="ki-duotone ki-folder fs-2x text-primary me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <!--end::Symbol-->
                    <!--begin::Title-->
                    <div class="d-flex flex-column justify-content-start fw-semibold">
                        <span class="fs-6 fw-semibold">{{ $folder->folder_name }}</span>
                    </div>
                    <!--end::Title-->
                </a>
                @endforeach
                <!--end::Item-->
                @endif

                @if(count($documentResults) > 0)
                <!--begin::Category title-->
                <h3 class="fs-5 text-muted m-0 pb-5" data-kt-search-element="category-title">Files</h3>
                <!--end::Category title-->
                <!--begin::Item-->
                @foreach($documentResults as $document)

                <a href="{{ route('file.user', $document->latest_version_guid) }}" class="d-flex text-gray-900 text-hover-primary align-items-center mb-5">
                    <!--begin::Symbol-->
                    <span class="icon-wrapper">
                        @switch($document->doc_type)
                        @case('pdf')
                        <img src="{{ asset('assets\media\icons\duotune\files\pdf-file.png') }} " class="mw-30px me-4"
                            alt="" />
                        @break
                        @case('docx')
                        @case('doc')
                        <img src="{{ asset('assets\media\icons\duotune\files\word-file.png') }} " class="mw-30px me-4"
                            alt="" />
                        @break
                        @case('xlsx')
                        @case('csv')
                        <img src="{{ asset('assets\media\icons\duotune\files\excel-file.png') }} " class="mw-30px me-4"
                            alt="" />

                        @break
                        @case('pptx')
                        <img src="{{ asset('assets\media\icons\duotune\files\pptx-file.png') }} " class="mw-30px me-4"
                            alt="" />

                        @break
                        @case('images')
                        <img src="{{ asset('assets\media\icons\duotune\files\image-file.png') }} " class="mw-25px me-4"
                            alt="" />

                        @break
                        @endswitch
                    </span>
                    <!--end::Symbol-->
                    <!--begin::Title-->
                    <div class="d-flex flex-column justify-content-start fw-semibold">
                        <span class="fs-6 fw-semibold">{{ $document->doc_title }}</span>
                    </div>
                    <!--end::Title-->
                </a>
                @endforeach

                <!--end::Item-->
                @endif

            </div>
            <!--end::Items-->
        </div>
        @endif

    </form>
</div>
