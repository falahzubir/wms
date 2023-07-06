<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        @can('view.dashboard')
            <li class="nav-item">
                <a class="nav-link {{ Request::segment(1) != 'dashboard' ? 'collapsed' : '' }}" href="/dashboard">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li><!-- End Dashboard Nav -->
        @endcan

        <li class="nav-heading">Orders</li>

        @can('view.overall_list')
            <li class="nav-item">
                <a class="nav-link {{ Route::current()->getName() != 'orders.overall' ? 'collapsed' : '' }}"
                    href="{{ route('orders.overall') }}">
                    <i class="bi bi-inboxes"></i>
                    <span>Overall List</span>
                </a>
            </li><!-- End Overall Order Nav -->
        @endcan

        @can('view.pending_list')
            <li class="nav-item">
                <a class="nav-link {{ Route::current()->getName() != 'orders.pending' ? 'collapsed' : '' }}"
                    href="{{ route('orders.pending') }}">
                    <i class="bi bi-clock-history"></i>
                    <span>Pending List</span>
                </a>
            </li><!-- End Pending List Nav -->
        @endcan

        @can('view.bucket_list')
            <li class="nav-item">
                <a class="nav-link  {{ Route::current()->getName() != 'buckets.index' ? 'collapsed' : '' }}"
                    href="{{ route('buckets.index') }}">
                    <i class="bi bi-basket"></i>
                    <span>Bucket List</span>
                </a>
            </li><!-- End Bucket List Nav -->
        @endcan

        @can('view.packing_list')
            <li class="nav-item">
                <a class="nav-link {{ Route::current()->getName() != 'orders.packing' ? 'collapsed' : '' }}"
                    href="{{ route('orders.packing') }}">
                    <i class="bi bi-box-seam"></i>
                    <span>Packing List</span>
                </a>
            </li><!-- End Packing List Nav -->
        @endcan

        @can('view.rts_list')
            <li class="nav-item">
                <a class="nav-link {{ Route::current()->getName() != 'orders.readyToShip' ? 'collapsed' : '' }}"
                    href="{{ route('orders.readyToShip') }}">
                    <i class="bi bi-truck-flatbed"></i>
                    <span>Pending Shipping List</span>
                </a>
            </li><!-- End Packing List Nav -->
        @endcan

        @can('view.shipping_list')
            <li class="nav-item">
                <a class="nav-link {{ Route::current()->getName() != 'orders.shipping' ? 'collapsed' : '' }}"
                    href="{{ route('orders.shipping') }}">
                    <i class="bi bi-truck"></i>
                    <span>In Transit List</span>
                </a>
            </li><!-- End Shipping List Nav -->
        @endcan

        @can('view.delivered_list')
            <li class="nav-item">
                <a class="nav-link {{ Route::current()->getName() != 'orders.delivered' ? 'collapsed' : '' }}"
                    href="{{ route('orders.delivered') }}">
                    <i class="bi bi-check-circle"></i>
                    <span>Delivered List</span>
                </a>
            </li><!-- End delivered List Nav -->
        @endcan

        @can('view.return_list')
            <li class="nav-item">
                <a class="nav-link {{ Route::current()->getName() != 'orders.returned' ? 'collapsed' : '' }}"
                    href="{{ route('orders.returned') }}">
                    <i class="bi bi-arrow-return-left"></i>
                    <span>Return List</span>
                </a>
            </li><!-- End return List Nav -->
        @endcan

        @can('view.reject_list')
            <li class="nav-item">
                <a class="nav-link {{ Route::current()->getName() != 'orders.rejected' ? 'collapsed' : '' }}"
                    href="{{ route('orders.rejected') }}">
                    <i class="bi bi-file-x"></i>
                    <span>Reject List</span>
                </a>
            </li><!-- End return List Nav -->
        @endcan

        @can('view.scan_parcel')
            <!-- Scan Parcel link -->
            <li class="nav-item">
                <a class="nav-link {{ Route::current()->getName() != 'orders.scan' ? 'collapsed' : '' }}"
                    href="{{ route('orders.scan') }}">
                    <i class="bx bx-barcode-reader"></i>
                    <span>Scan Parcel</span>
                </a>
            </li><!-- End Scan Parcel Nav -->
        @endcan

        @can('view.settings')
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-menu-button-wide"></i><span>Settings</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="components-nav"
                    class="nav-content {{ Route::current()->getName() != 'companies.index' ? 'collapsed' : '' }} "
                    data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="{{ route('companies.index') }}"
                            {{ Route::current()->getName() == 'companies.index' ? 'class=active' : '' }}>
                            <i class="bi bi-circle"></i>
                            <span>Companies</span>
                        </a>
                    </li>
                    @can('permission.update')
                        <li>
                            <a href="{{ route('roles.index') }}"
                                {{ Route::current()->getName() == 'roles.index' ? 'class=active' : '' }}>
                                <i class="bi bi-circle"></i><span>Roles</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('orders.change_postcode_view')}}"
                                {{ Route::current()->getName() == 'orders.change_postcode_view' ? 'class=active' : '' }}>
                                <i class="bi bi-circle"></i><span>Change Postcode</span>
                            </a>
                        </li>
                    @endcan
                    @can('operational_model.update')
                        <li>
                            <a href="{{ route('operational_model.index') }}"
                                {{ Route::current()->getName() == 'operational_model.index' ? 'class=active' : '' }}>
                                <i class="bi bi-circle"></i><span>Operational Model</span>
                            </a>
                        </li>
                    @endcan
                    <li>
                        <a href="{{ route('users.index') }}"
                            {{ Route::current()->getName() == 'users.index' ? 'class=active' : '' }}>
                            <i class="bi bi-circle"></i><span>Users</span>
                        </a>
                    </li>
                </ul>
            </li><!-- End Components Nav -->
        @endcan

        {{-- <li class="nav-item">
            <a class="nav-link collapsed" href="users-profile.html">
                <i class="bi bi-person"></i>
                <span>Profile</span>
            </a>
        </li><!-- End Profile Page Nav --> --}}

        {{-- <li class="nav-item">
        <a class="nav-link collapsed" href="pages-faq.html">
          <i class="bi bi-question-circle"></i>
          <span>F.A.Q</span>
        </a>
      </li><!-- End F.A.Q Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-contact.html">
          <i class="bi bi-envelope"></i>
          <span>Contact</span>
        </a>
      </li><!-- End Contact Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="file:///{{ base_path() }}/resources/template/index.html" >
          <i class="bi bi-card-list"></i>
          <span>Example Pages</span>
        </a>
      </li><!-- End Register Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="/login">
          <i class="bi bi-box-arrow-in-right"></i>
          <span>Login</span>
        </a>
      </li><!-- End Login Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="/404">
          <i class="bi bi-dash-circle"></i>
          <span>Error 404</span>
        </a>
      </li><!-- End Error 404 Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-blank.html">
          <i class="bi bi-file-earmark"></i>
          <span>Blank</span>
        </a>
      </li><!-- End Blank Page Nav --> --}}

    </ul>

</aside><!-- End Sidebar-->
