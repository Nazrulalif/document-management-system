<ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold ">
    <!--begin:::Tab item-->
    <li class="nav-item">
        <a class="nav-link text-active-primary pb-4 {{ (Request::is('admin/user-list') ? 'active' : '') }}  "
            href="{{ route('user.index') }}">Active</a>
    </li>
    <!--end:::Tab item-->
    <!--begin:::Tab item-->
    <li class="nav-item">
        <a class="nav-link text-active-primary pb-4 {{ (Request::is('admin/deactivated-list') ? 'active' : '') }} "
            href="{{ route('user.deactivated.index') }}">Deactivated</a> 
    </li>
   
</ul>
