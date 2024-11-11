@extends('admin.setting.setting')

@section('setting_content')
<style>
    .image-input .image-input-wrapper {
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
    }

</style>
<form action="{{ route('setting.index.post') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-3">
            <label class="col-form-label fw-semibold fs-6">System Name</label>
        </div>
        <div class="col-md-9">
            <input type="text" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" name="system_name"
                value="{{ $systemName && $systemName->attribute ? $systemName->attribute : 'DocMS' }}">
        </div>
    </div>
    <div class="separator separator-dashed my-6"></div>
    <div class="row">
        <div class="col-lg-6 mb-6 mb-lg-0">
            <div class="row">
                <div class="col-md-6">
                    <label class="col-form-label fw-semibold fs-6">Navbar logo
                        <div class="form-text">Light theme</div>
                    </label>
                </div>
                <div class="col-md-6">

                    <div class="image-input image-input-outline " data-kt-image-input="true"
                        style="background-image: url({{ $lightLogo && $lightLogo->attribute  ? url('/file/' . base64_encode($lightLogo->attribute)) : asset('assets/media/svg/avatars/blank.svg') }})">
                        <div class="image-input-wrapper w-125px h-125px bgi-size-contain" style="background-image: url({{ $lightLogo && $lightLogo->attribute  ? url('/file/' . base64_encode($lightLogo->attribute)) : asset('assets/media/svg/avatars/blank.svg') }})
                            ">
                        </div>

                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                            data-kt-image-input-action="change" data-bs-toggle="tooltip" aria-label="Change avatar"
                            data-bs-original-title="Change avatar" data-kt-initialized="1">
                            <i class="ki-duotone ki-pencil fs-7">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="file" name="nav_light_file" accept=".png, .jpg, .jpeg, .svg">
                            <input type="hidden" name="nav_light" value="nav_light">
                            <input type="hidden" name="avatar_remove">
                        </label>
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip" aria-label="Cancel avatar"
                            data-bs-original-title="Cancel avatar" data-kt-initialized="1">
                            <i class="ki-duotone ki-cross fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>

                    </div>
                    <div class="form-text">Allowed file types: png, jpg, jpeg, svg.</div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="row">
                <div class="col-md-6">
                    <label class="col-form-label fw-semibold fs-6">Navbar logo
                        <div class="form-text">Dark theme</div>
                    </label>
                </div>
                <div class="col-md-6">
                    <div class="image-input image-input-outline" data-kt-image-input="true"
                        style="background-image: url({{ $darkLogo && $darkLogo->attribute ? url('/file/' . base64_encode($darkLogo->attribute)) : asset('assets/media/svg/avatars/blank.svg') }})">
                        <div class="image-input-wrapper w-125px h-125px"
                            style="background-image: url({{ $darkLogo && $darkLogo->attribute ? url('/file/' . base64_encode($darkLogo->attribute)) : asset('assets/media/svg/avatars/blank.svg') }})">
                        </div>

                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                            data-kt-image-input-action="change" data-bs-toggle="tooltip" aria-label="Change avatar"
                            data-bs-original-title="Change avatar" data-kt-initialized="1">
                            <i class="ki-duotone ki-pencil fs-7">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="file" name="nav_dark_file" accept=".png, .jpg, .jpeg, .svg">
                            <input type="hidden" name="nav_dark" value="nav_dark">
                            <input type="hidden" name="avatar_remove">
                        </label>
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip" aria-label="Cancel avatar"
                            data-bs-original-title="Cancel avatar" data-kt-initialized="1">
                            <i class="ki-duotone ki-cross fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                    </div>
                    <div class="form-text">Allowed file types: png, jpg, jpeg, svg.</div>
                </div>
            </div>
        </div>
    </div>
    <div class="separator separator-dashed my-6"></div>
    <div class="row">
        <div class="col-lg-6 mb-6">
            <div class="row">
                <div class="col-md-6">
                    <label class="col-form-label fw-semibold fs-6">Favicon
                        <div class="form-text">Web and header icon</div>
                    </label>
                </div>
                <div class="col-md-6">

                    <div class="image-input image-input-outline " data-kt-image-input="true"
                        style="background-image: url({{ $favicon && $favicon->attribute  ? url('/file/' . base64_encode($favicon->attribute)) : asset('assets/media/svg/avatars/blank.svg') }})">
                        <div class="image-input-wrapper w-125px h-125px bgi-size-contain" style="background-image: url({{ $favicon && $favicon->attribute  ? url('/file/' . base64_encode($favicon->attribute)) : asset('assets/media/svg/avatars/blank.svg') }})
                            ">
                        </div>

                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                            data-kt-image-input-action="change" data-bs-toggle="tooltip" aria-label="Change avatar"
                            data-bs-original-title="Change avatar" data-kt-initialized="1">
                            <i class="ki-duotone ki-pencil fs-7">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="file" name="favicon_file" accept=".png, .jpg, .jpeg, .svg">
                            <input type="hidden" name="favicon" value="favicon">
                            <input type="hidden" name="avatar_remove">
                        </label>
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip" aria-label="Cancel avatar"
                            data-bs-original-title="Cancel avatar" data-kt-initialized="1">
                            <i class="ki-duotone ki-cross fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>

                    </div>
                    <div class="form-text">Allowed file types: png, jpg, jpeg, svg.</div>
                </div>
            </div>
        </div>
    </div>
    <div class="separator separator-dashed my-6"></div>
    <div class="row">
        <div class="col-md-3">
            <label class="col-form-label fw-semibold fs-6">Authentication logo caption</label>
        </div>
        <div class="col-md-9">
            <input type="text" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" name="logo_caption"
                value="{{ $logoCaption && $logoCaption->attribute ? $logoCaption->attribute : 'Simplifying Document Workflows' }}">
        </div>
    </div>
    <div class="separator separator-dashed my-6"></div>
    <div class="row">
        <div class="col-lg-6 mb-6">
            <div class="row">
                <div class="col-md-6">
                    <label class="col-form-label fw-semibold fs-6">Authentication background image</label>
                </div>
                <div class="col-md-6">

                    <div class="image-input image-input-outline " data-kt-image-input="true"
                        style="background-image: url({{ $loginBackground && $loginBackground->attribute  ? url('/file/' . base64_encode($loginBackground->attribute)) : asset('assets/media/svg/avatars/blank.svg') }})">
                        <div class="image-input-wrapper w-125px h-125px bgi-size-contain" style="background-image: url({{ $loginBackground && $loginBackground->attribute  ? url('/file/' . base64_encode($loginBackground->attribute)) : asset('assets/media/svg/avatars/blank.svg') }})
                            ">
                        </div>

                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                            data-kt-image-input-action="change" data-bs-toggle="tooltip" aria-label="Change avatar"
                            data-bs-original-title="Change avatar" data-kt-initialized="1">
                            <i class="ki-duotone ki-pencil fs-7">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="file" name="login_background_file" accept=".png, .jpg, .jpeg, .svg">
                            <input type="hidden" name="login_background" value="favicon">
                            <input type="hidden" name="avatar_remove">
                        </label>
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip" aria-label="Cancel avatar"
                            data-bs-original-title="Cancel avatar" data-kt-initialized="1">
                            <i class="ki-duotone ki-cross fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>

                    </div>
                    <div class="form-text">Allowed file types: png, jpg, jpeg, svg.</div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-6">
            <div class="row">
                <div class="col-md-6">
                    <label class="col-form-label fw-semibold fs-6">Authentication Logo</label>
                </div>
                <div class="col-md-6">

                    <div class="image-input image-input-outline " data-kt-image-input="true"
                        style="background-image: url({{ $loginLogo && $loginLogo->attribute  ? url('/file/' . base64_encode($loginLogo->attribute)) : asset('assets/media/svg/avatars/blank.svg') }})">
                        <div class="image-input-wrapper w-125px h-125px bgi-size-contain" style="background-image: url({{ $loginLogo && $loginLogo->attribute  ? url('/file/' . base64_encode($loginLogo->attribute)) : asset('assets/media/svg/avatars/blank.svg') }})
                            ">
                        </div>

                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                            data-kt-image-input-action="change" data-bs-toggle="tooltip" aria-label="Change avatar"
                            data-bs-original-title="Change avatar" data-kt-initialized="1">
                            <i class="ki-duotone ki-pencil fs-7">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="file" name="login_logo_file" accept=".png, .jpg, .jpeg, .svg">
                            <input type="hidden" name="login_logo" value="favicon">
                            <input type="hidden" name="avatar_remove">
                        </label>
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip" aria-label="Cancel avatar"
                            data-bs-original-title="Cancel avatar" data-kt-initialized="1">
                            <i class="ki-duotone ki-cross fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>

                    </div>
                    <div class="form-text">Allowed file types: png, jpg, jpeg, svg.</div>
                </div>
            </div>
        </div>
    </div>
    <div class="separator separator-dashed my-6"></div>
    <div class="row">
        <div class="col-lg-6 mb-6">
            <div class="row">
                <div class="col-md-6">
                    <label class="col-form-label fw-semibold fs-6">Page Loader</label>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" value="Y" id="flexSwitchChecked" name="page_loader" 
                        {{ $pageLoader && $pageLoader->attribute == 'Y' ? 'checked' : '' }}/>
                        <label class="form-check-label" for="flexSwitchChecked">
                            On
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-end py-6 px-9">
        <button type="reset" class="btn btn-light btn-active-light-primary me-2">Discard</button>
        <button type="submit" class="btn btn-primary" id="kt_account_profile_details_submit">Save
            Changes</button>
    </div>
</form>


@endsection
