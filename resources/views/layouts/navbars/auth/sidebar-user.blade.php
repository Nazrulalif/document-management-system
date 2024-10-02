<!--begin::Sidebar-->
<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!--begin::Logo-->
    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        <!--begin::Logo image-->
        <a href="{{ route('home.user') }}">
            <img alt="Logo" src="{{ asset('assets/media/logos/default.svg') }}"
                class="h-25px app-sidebar-logo-default theme-light-show" />
            <img alt="Logo" src="{{ asset('assets/media/logos/default-small.svg') }}"
                class="h-20px app-sidebar-logo-minimize" />
            <img alt="Logo" src="{{ asset('assets/media/logos/default-dark.svg') }}"
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
                        <a class="menu-link {{ (Request::is('home', 'my-profile', 'my-file', 'setting') ? 'active' : '') }}"
                            href="{{ route('home.user') }}">
                            <span class="menu-icon">
                                <i class="ki-outline ki-home fs-2">
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span class="menu-title">Home</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ (Request::is('file-manager', 'file-manager/*', 'file-details/*') ? 'active' : '') }}"
                            href="{{ route('file-manager.user') }}">
                            <span class="menu-icon">
                                <i class="ki-outline ki-switch fs-2">
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">File Manager</span>
                        </a>
                        <!--end:Menu link-->
                    </div>

                    <div class="menu-item pt-5">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ (Request::is('starred') ? 'active' : '') }}"
                            href="{{ route('starred.user') }}">
                            <span class="menu-icon">
                                <i class="ki-outline ki-star fs-2">
                                </i>
                            </span>
                            <span class="menu-title">Starred</span>
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
