<ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8">
    <!--begin:::Tab item-->
    <li class="nav-item">
        <a class="nav-link text-active-primary pb-4  {{ (Request::is('admin/company-detail/*') ? 'active' : '') }} "
            href="{{ route('company.view', $data->uuid) }}">Active Users</a>
    </li>
    <!--end:::Tab item-->
    <!--begin:::Tab item-->
    <li class="nav-item">
        <a class="nav-link text-active-primary pb-4 {{ (Request::is('admin/company-file/*') ? 'active' : '') }}" 
            href="{{ route('company.file', $data->uuid) }}">Files</a>
    </li>
    <!--end:::Tab item-->
    <li class="nav-item">
        <a class="nav-link text-active-primary pb-4 {{ (Request::is('admin/company-setting/*') ? 'active' : '') }}" 
            href="{{ route('company.setting', $data->uuid) }}">Settings</a>
    </li>
</ul>
