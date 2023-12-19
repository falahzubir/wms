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
        <a class="nav-link collapsed" data-bs-target="#return-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-menu-button-wide"></i><span>Return List</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="return-nav"
            class="nav-content {{ Route::current()->getName() != 'orders.returned' ? 'collapsed' : '' }} "
            data-bs-parent="#sidebar-nav">
            <li>
                <a href="{{ route('orders.returned') }}"
                    {{ Route::current()->getName() == 'orders.returned' ? 'class=active' : '' }}>
                    <i class="bi bi-circle"></i>
                    <span>Pending</span>
                </a>
            </li>
            <li>
                <a href="{{ route('orders.return_completed') }}"
                    {{ Route::current()->getName() == 'orders.return_completed' ? 'class=active' : '' }}>
                    <i class="bi bi-circle"></i>
                    <span>Completed</span>
                </a>
            </li>
        </ul>
        @endcan

        @can('view.claim_list')
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#claims-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-menu-button-wide"></i><span>Claim List</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="claims-nav"
                class="nav-content {{ Route::current()->getName() != 'claims.index' ? 'collapsed' : '' }} "
                data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('claims.product.index') }}"
                        {{ Route::current()->getName() == 'claims.product.index' ? 'class=active' : '' }}>
                        <i class="bi bi-circle"></i>
                        <span>Product</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('claims.courier.index') }}"
                        {{ Route::current()->getName() == 'claims.courier.index' ? 'class=active' : '' }}>
                        <i class="bi bi-circle"></i>
                        <span>Courier</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Components Nav -->
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

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#components-nav-scan" data-bs-toggle="collapse" href="#">
                <i class="bx bx-barcode-reader"></i><span>Scan Parcel</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components-nav-scan"
                class="nav-content {{ Route::current()->getName() != 'orders.scan' ? 'collapsed' : '' }} "
                data-bs-parent="#sidebar-nav">
                @can('view.scan_parcel')
                    <li>
                        <a class="nav-link {{ Route::current()->getName() != 'orders.scan' ? 'collapsed' : '' }}"
                            href="{{ route('orders.scan') }}">
                            <i class="bx bx-barcode-reader"></i>
                            <span>Scan</span>
                        </a>
                    </li>
                @endcan
                @can('view.scan_setting')
                    <li>
                        <a class="nav-link {{ Route::current()->getName() != 'orders.scan_setting' ? 'collapsed' : '' }}"
                            href="{{ route('orders.scan_setting') }}">
                            <i class="bx bx-barcode-reader"></i>
                            <span>Setting</span>
                        </a>
                    </li>
                @endcan
            </ul>
        </li><!-- End Components Nav -->

        @can('product.list')
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#components-nav-inventory" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-menu-button-wide"></i><span>Inventory</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="components-nav-inventory"
                    class="nav-content {{ Route::current()->getName() != 'inventory.index' ? 'collapsed' : '' }} "
                    data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="{{ route('products.index') }}"
                            {{ Route::current()->getName() == 'products.index' ? 'class=active' : '' }}>
                            <i class="bi bi-circle"></i>
                            <span>Product</span>
                        </a>
                    </li>
                </ul>
            </li><!-- End Components Nav -->
        @endcan

        @can('report.view')
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#components-nav-report" data-bs-toggle="collapse" href="#">
                <i class="bi bi-menu-button-wide"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components-nav-report"
                class="nav-content {{ Route::current()->getName() != 'reports.index' ? 'collapsed' : '' }} "
                data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('reports.sla') }}"
                        {{ Route::current()->getName() == 'reports.sla' ? 'class=active' : '' }}>
                        <i class="bi bi-circle"></i>
                        <span>Service Level Agreement (SLA)</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.outbound') }}"
                        {{ Route::current()->getName() == 'reports.outbound' ? 'class=active' : '' }}>
                        <i class="bi bi-circle"></i>
                        <span>Outbound</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.order_matrix') }}"
                        {{ Route::current()->getName() == 'reports.order_matrix' ? 'class=active' : '' }}>
                        <i class="bi bi-circle"></i>
                        <span>Order Matrix</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.pending_report') }}"
                        {{ Route::current()->getName() == 'reports.pending_report' ? 'class=active' : '' }}>
                        <i class="bi bi-circle"></i>
                        <span>Pending Report</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link collapsed" data-bs-target="#components-nav-report-shipment" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-menu-button-wide"></i><span>Shipment</span><i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="components-nav-report-shipment"
                    class=" {{ Route::current()->getName() != 'reports.index' ? 'collapsed' : '' }} ">
                        <li>
                            <a href="{{ route('reports.shipment.attempt-list') }}"
                                {{ Route::current()->getName() == 'reports.shipment.attempt-list' ? 'class=active' : '' }}>
                                <i class="bi bi-circle"></i>
                                <span>Attempt List</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.shipment.unattempt-list') }}"
                                {{ Route::current()->getName() == 'reports.shipment.unattempt-list' ? 'class=active' : '' }}>
                                <i class="bi bi-circle"></i>
                                <span>Unattempt List</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.shipment.problematic-list') }}"
                                {{ Route::current()->getName() == 'reports.shipment.problematic-list' ? 'class=active' : '' }}>
                                <i class="bi bi-circle"></i>
                                <span>Problematic List</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li><!-- End Components Nav -->
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
                        <a href="{{ route('alternative_postcode.index') }}"
                            {{ Route::current()->getName() == 'alternative_postcode.index' ? 'class=active' : '' }}>
                            <i class="bi bi-circle"></i><span>Alternative Postcode</span>
                        </a>
                    </li>
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
                    @role('IT_Admin')
                    <li>
                        <a href="{{ route('settings.index') }}"
                            {{ Route::current()->getName() == 'settings.index' ? 'class=active' : '' }}>
                            <i class="bi bi-circle"></i><span>Settings</span>
                        </a>
                    </li>
                    @endrole
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
