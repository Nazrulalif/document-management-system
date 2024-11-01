<div class="d-flex flex-column flex-lg-row">
    <!--begin::Aside-->
    <div class="flex-column flex-lg-row-auto w-100 w-lg-250px w-xxl-325px mb-8 mb-lg-0 me-lg-9 me-5">
        <!--begin::Form-->
            <!--begin::Card-->
            <div class="card  ">
                <!--begin::Body-->
                <div class="card-body ">
                    <h3 class="fw-bold my-1">Filter By</h3>
                    <!--begin::Border-->
                    <div class="separator separator-dashed my-8"></div>
                    <!--end::Border-->
                    <!--begin::Input group-->
                    <div class="scrollable-container">
                        <div class="mb-5">
                            <label class="fs-6 form-label fw-bold text-gray-900 mb-5">Type</label>

                            <div class="form-check form-check-custom form-check-solid form-check-sm mb-3">
                                <input class="form-check-input" type="radio" value="folder" name="search_type"
                                    wire:model.live="selectedType" id="flexRadioFolder" />
                                <label class="form-check-label flex-grow-1 fw-semibold text-gray-700 fs-6"
                                    for="flexRadioFolder">
                                    Folder
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid form-check-sm">
                                <input class="form-check-input" type="radio" value="file" name="search_type"
                                    wire:model.live="selectedType" id="flexRadioFile" />
                                <label class="form-check-label flex-grow-1 fw-semibold text-gray-700 fs-6"
                                    for="flexRadioFile">
                                    File
                                </label>
                            </div>

                        </div>
                        @if($selectedType === 'file')
                        <div class="mb-5">
                            <label class="fs-6 form-label fw-bold text-gray-900">File type</label>
                            <!--begin::Select-->
                            <select class="form-select form-select-solid" wire:model.live="fileType">
                                <option value="">All</option>
                                <option value="pdf">PDF</option>
                                <option value="xlsx">XLSX</option>
                                <option value="ppt">PPT</option>
                                <option value="docx">DOCX</option>
                                <option value="images">IMAGES</option>
                            </select>
                            <!--end::Select-->
                        </div>
                        @endif

                        @if(Auth::user()->role_guid == 1)
                        <div class="mb-10">
                            <label class="fs-6 form-label fw-bold text-gray-900 mb-5">Company</label>

                            @foreach ($companyList as $item)
                            <!--begin::Checkbox-->
                            <div class="form-check form-check-custom form-check-solid mb-5">
                                <input class="form-check-input" type="checkbox" id="company_{{ $item->id }}"
                                    value="{{ $item->id }}" wire:model.live="companies" />
                                <label class="form-check-label flex-grow-1 fw-semibold text-gray-700 fs-6"
                                    for="company_{{ $item->id }}">{{ $item->org_name }}</label>
                            </div>
                            <!--end::Checkbox-->
                            @endforeach

                        </div>
                        @endif

                    </div>

                </div>
                <!--end::Body-->
            </div>
            <!--end::Card-->
    </div>
    <!--end::Aside-->

    <!--begin::Layout-->
    <div class="flex-lg-row-fluid">
        <!--begin:Search-->
        {{-- <form wire:submit.prevent="search">
            <div class="d-flex">
                <div class="input-group mb-4">
                    <input type="text" wire:model="query" class="form-control form-control-solid"
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
        </form> --}}
        <!--end:Search-->
        @if ($query)
        <!--begin::Tab Content-->
        <div class="tab-content">
            <div class="fv-row">
                @if ($results->total() >= 0)
                <div class="mb-4">
                    <h4>{{ $results->total() }} Results</h4>
                </div>
                @endif

                @forelse ($results as $result)
                <div class="fv-row mb-3">
                    <div class="card">
                        <div class="card-body p-5">
                            <div class="d-flex align-items-center">
                                <span class="icon-wrapper">
                                    @if ($selectedType === 'file')
                                    @switch($result->doc_type)
                                    @case('pdf')
                                    <img src="{{ asset('assets\media\icons\duotune\files\pdf-file.png') }} " class="mw-30px me-4" alt="" />
                                    @break
                                    @case('docx')
                                    @case('doc')
                                    <img src="{{ asset('assets\media\icons\duotune\files\word-file.png') }} " class="mw-30px me-4" alt="" />
                                    @break
                                    @case('xlsx')
                                    @case('csv')
                                    <img src="{{ asset('assets\media\icons\duotune\files\excel-file.png') }} " class="mw-30px me-4" alt="" />

                                    @break
                                    @case('pptx')
                                    <img src="{{ asset('assets\media\icons\duotune\files\pptx-file.png') }} " class="mw-30px me-4" alt="" />

                                    @break
                                    @case('images')
                                    <img src="{{ asset('assets\media\icons\duotune\files\image-file.png') }} " class="mw-25px me-4" alt="" />

                                    @break
                                    @endswitch
                                    @else
                                    <i class="ki-duotone ki-folder fs-2x text-primary me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    @endif
                                </span>
                                <a href="{{ $selectedType === 'file' ? route('file.user', $result->latest_version_guid) : route('folder.show.user', $result->uuid) }}"
                                    class="text-gray-800 text-hover-primary">{!! $result->highlighted_title !!}</a>
                            </div>

                            <div class="fv-row mt-3">
                                @if ($selectedType === 'file')
                                <div class="fs-7 my-2 text-gray-600">
                                    {!! $result->highlighted_content !!}
                                </div>
                                <div class="fs-7 my-2 text-gray-600">
                                    @foreach ($result->highlighted_keywords as $keyword)
                                    <span class="badge badge-outline badge-secondary">{!! $keyword !!}</span>
                                    @endforeach
                                </div>
                                @endif

                                <div class="fs-7 text-gray-600">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-30px symbol-circle me-3">
                                            <img src="{{ $result->profile_picture ? asset('storage/' . $result->profile_picture) : asset('assets/media/svg/avatars/blank.svg') }}" alt="user">

                                        </div>
                                        {{ $result->full_name }} | Shared to: {{ $result->shared_orgs }} |
                                        {{ $result->created_at->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="d-flex flex-column flex-center">
                    <img src="{{ asset('assets/media/illustrations/sketchy-1/5.png') }}" class="mw-300px"
                        alt="No results found illustration">
                    <div class="fs-1 fw-bolder">No items found.</div>
                    <div class="fs-6">Please enter the correct keyword!</div>
                </div>
                @endforelse
                <!-- Pagination Links -->
                @if ($results->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $results->links('livewire::bootstrap') }}
                </div>
                @endif
            </div>
        </div>

        <!--end::Tab Content-->
        @else
        <div class="d-flex flex-column flex-center">
            <img src="{{ asset('assets/media/illustrations/sketchy-1/5.png') }}" class="mw-300px" alt="">
            <div class="fs-1 fw-bolder text-dark">Enter keyword to Search</div>
            <div class="fs-6">Start searching your folders or files!</div>
        </div>
        @endif

    </div>

</div>
