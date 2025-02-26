<!--begin::Sidebar-->
<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!--begin::Logo-->
    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        @php
        // Retrieve the logos from the database directly in the view
        $lightLogo = \App\Models\SystemSetting::where('name', 'nav_light_logo')->first();
        $darkLogo = \App\Models\SystemSetting::where('name', 'nav_dark_logo')->first();
        @endphp
        <!--begin::Logo image-->
        <a href="{{ route('dashboard.admin') }}">
            <img alt="Logo"
                src="{{ $lightLogo && $lightLogo->attribute ? url('/file/' . base64_encode($lightLogo->attribute))  : asset('assets/media/logos/docms-light.svg') }}"
                class="h-25px app-sidebar-logo-default theme-light-show" />

            <img alt="Logo"
                src="{{ $lightLogo && $lightLogo->attribute ? url('/file/' . base64_encode($lightLogo->attribute))  : asset('assets/media/logos/docms-light.svg') }}"
                class="h-20px app-sidebar-logo-minimize" />

            <img alt="Logo"
                src="{{ $darkLogo && $darkLogo->attribute ? url('/file/' . base64_encode($darkLogo->attribute))  : asset('assets/media/logos/docms-dark.svg') }}"
                class="h-25px app-sidebar-logo-default theme-dark-show">
        </a>
        <div id="kt_app_sidebar_toggle"
            class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate"
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="app-sidebar-minimize">
            <i class="ki-duotone ki-black-left-line fs-3 rotate-180">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </div>
        <!--end::Sidebar toggle-->
    </div>
    <!--end::Logo-->
    <!--begin::sidebar menu-->
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        <!--begin::Menu wrapper-->
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
            <!--begin::Scroll wrapper-->
            <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true"
                data-kt-scroll-activate="true" data-kt-scroll-height="auto"
                data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
                data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px"
                data-kt-scroll-save-state="true">
                <!--begin::Menu-->
                <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_app_sidebar_menu"
                    data-kt-menu="true" data-kt-menu-expand="false">
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ (Request::is('admin/dashboard', 'admin/my-profile', 'admin/my-file', 'admin/setting') ? 'active' : '') }}"
                            href="{{ route('dashboard.admin') }}">
                            <span class="menu-icon">
                                <i class="ki-outline ki-element-11 fs-2">
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span class="menu-title">Dashboard</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <div class="menu-item pt-5">
                        <!--begin:Menu content-->
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">Tools</span>
                        </div>
                        <!--end:Menu content-->
                    </div>
                    <div class="menu-item ">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ (Request::is('admin/advance-search') ? 'active' : '') }}"
                            href="{{ route('search.index') }}">
                            <span class="menu-icon">
                                <i class="ki-outline ki-magnifier fs-2">
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span class="menu-title">Advance Search</span>
                        </a>
                        <!--end:Menu link-->
                    </div>

                    <div class="menu-item pt-5">
                        <!--begin:Menu content-->
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">Apps</span>
                        </div>
                        <!--end:Menu content-->
                    </div>

                    @if(Auth::user()->role_guid != 3)
                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion {{ (Request::is('admin/company-list', 'admin/company-detail/*', 
                    'admin/user-list', 'admin/user-detail/*', 'admin/role-list', 'admin/view-role/*',
                    'admin/user-setting/*','admin/user-file/*', 'admin/company-file/*', 'admin/company-setting/*') ? 'hover show' : '') }}">
                        <!--begin:Menu link-->
                        <span class="menu-link {{ (Request::is('admin/company-list', 'admin/company-detail/*', 
                        'admin/user-list', 'admin/user-detail/*', 'admin/role-list', 'admin/view-role/*', 
                        'admin/company-file/*', 'admin/company-setting/*') ? 'active' : '') }}">
                            <span class="menu-icon">
                                <i class="ki-outline ki-user fs-2">
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">User Management</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <!--end:Menu link-->
                        <!--begin:Menu sub-->

                        <div class="menu-sub menu-sub-accordion">
                            <!--begin:Menu item-->
                            <div class="menu-item">

                                @if (Auth::user()->role_guid == 1)
                                <!--begin:Menu link-->
                                <a class="menu-link {{ (Request::is('admin/company-list', 'admin/company-detail/*',
                            'admin/company-file/*', 'admin/company-setting/*') ? 'active' : '') }}"
                                    href="{{ route('company.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Company List</span>
                                </a>
                                <!--end:Menu link-->
                                @endif

                                <!--begin:Menu link-->
                                <a class="menu-link {{ (Request::is('admin/user-list', 'admin/user-detail/*',
                            'admin/user-setting/*','admin/user-file/*') ? 'active' : '') }}"
                                    href="{{ route('user.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">User List</span>
                                </a>
                                <!--end:Menu link-->

                                @if (Auth::user()->role_guid == 1)
                                <!--begin:Menu link-->
                                <a class="menu-link {{ (Request::is('admin/role-list', 'admin/view-role/*') ? 'active' : '') }}"
                                    href="{{ route('role.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Role List</span>
                                </a>
                                <!--end:Menu link-->
                                @endif
                            </div>

                        </div>
                        <!--end:Menu sub-->
                    </div>
                    @endif

                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ (Request::is('admin/file-manager','admin/file-manager/*', 'admin/folder/*', 'admin/file-details/*') ? 'active' : '') }}"
                            href="{{ route('fileManager.index') }}">
                            <span class="menu-icon">
                                <i class="ki-outline ki-switch fs-2">
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">File Manager</span>
                        </a>
                        <!--end:Menu link-->
                    </div>

                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ (Request::is('admin/starred') ? 'active' : '') }}"
                            href="{{ route('starred.index') }}">
                            <span class="menu-icon">
                                <i class="ki-outline ki-star fs-2">
                                </i>
                            </span>
                            <span class="menu-title">Starred</span>
                        </a>
                        <!--end:Menu link-->
                    </div>

                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ (Request::is('admin/report', 'admin/generated-report') ? 'active' : '') }}"
                            href="{{ route('report.index') }}">
                            <span class="menu-icon">
                                <i class="ki-outline ki-file fs-2">
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Report</span>
                        </a>
                        <!--end:Menu link-->
                    </div>

                    @if (Auth::user()->role_guid == 1)
                    {{-- <div class="menu-item pt-5">
                        <!--begin:Menu content-->
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">System Setting</span>
                        </div>
                        <!--end:Menu content-->
                    </div> --}}

                    {{-- <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ (Request::is('admin/system-setting',) ? 'active' : '') }}"
                            href="{{ route('setting.index') }}">
                            <span class="menu-icon">
                                <i class="ki-outline ki-gear fs-2"></i>
                            </span>
                            <span class="menu-title">Settings</span>
                        </a>
                        <!--end:Menu link-->
                    </div> --}}

                    @endif


                    <div class="menu-item pt-5">
                        <!--begin:Menu content-->
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">Sign out</span>
                        </div>
                        <!--end:Menu content-->
                    </div>

                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link" href="{{ route('logout') }}">
                            <span class="menu-icon">
                                <i class="ki-outline ki-exit-left fs-2">
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Sign out</span>
                        </a>
                        <!--end:Menu link-->
                    </div>




                </div>
                <!--end::Menu-->

            </div>
            <!--end::Scroll wrapper-->
        </div>
        <!--end::Menu wrapper-->
    </div>
    <!--end::sidebar menu-->
</div>
<!--end::Sidebar-->
