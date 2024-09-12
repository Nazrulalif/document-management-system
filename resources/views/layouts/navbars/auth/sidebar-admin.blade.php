<!--begin::Sidebar-->
<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!--begin::Logo-->
    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        <!--begin::Logo image-->
        <a href="index.html">
            <img alt="Logo" src="{{ asset('assets/media/logos/default.svg') }}"
                class="h-25px app-sidebar-logo-default" />
            <img alt="Logo" src="{{ asset('assets/media/logos/default-small.svg') }}"
                class="h-20px app-sidebar-logo-minimize" />
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
                        <a class="menu-link {{ (Request::is('admin/dashboard') ? 'active' : '') }}"
                            href="{{ route('dashboard.admin') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-element-11 fs-2">
                                    <span class="path1"></span>
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
                    <div class="menu-item {{ (Request::is('admin/advance-search') ? 'active' : '') }}">
                        <!--begin:Menu link-->
                        <a class="menu-link" href="/">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-magnifier fs-2">
                                    <span class="path1"></span>
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

                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion {{ (Request::is('admin/company-list', 'admin/company-detail/*', 'admin/user-list', 'admin/user-detail/*', 'admin/role-list', 'admin/view-role/*') ? 'hover show' : '') }}">
                        <!--begin:Menu link-->
                        <span
                            class="menu-link {{ (Request::is('admin/company-list', 'admin/company-detail/*', 'admin/user-list', 'admin/user-detail/*', 'admin/role-list', 'admin/view-role/*') ? 'active' : '') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-user fs-2">
                                    <span class="path1"></span>
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
                                <!--begin:Menu link-->
                                <a class="menu-link {{ (Request::is('admin/company-list', 'admin/company-detail/*') ? 'active' : '') }}"
                                    href="{{ route('company.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Company List</span>
                                </a>
                                <!--end:Menu link-->
                                <!--begin:Menu link-->
                                <a class="menu-link {{ (Request::is('admin/user-list', 'admin/user-detail/*') ? 'active' : '') }}"
                                    href="{{ route('user.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">User List</span>
                                </a>
                                <!--end:Menu link-->

                                <!--begin:Menu link-->
                                <a class="menu-link {{ (Request::is('admin/role-list', 'admin/view-role/*') ? 'active' : '') }}" href="{{ route('role.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Role List</span>
                                </a>
                                <!--end:Menu link-->
                            </div>

                        </div>
                        <!--end:Menu sub-->
                    </div>

                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ (Request::is('admin/file-manager', 'admin/folder/*') ? 'active' : '') }}" href="{{ route('fileManager.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-switch fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">File Manager</span>
                        </a>
                        <!--end:Menu link-->
                    </div>

                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link" href="{{ route('dashboard.admin') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-file fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Report</span>
                        </a>
                        <!--end:Menu link-->
                    </div>

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
                                <i class="ki-duotone ki-exit-left fs-2">
                                    <span class="path1"></span>
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
