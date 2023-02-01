<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link {{ Request::segment(1) != 'dashboard' ? 'collapsed' : '' }}" href="/dashboard">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li><!-- End Dashboard Nav -->

        <li class="nav-heading">Orders</li>

        <li class="nav-item">
            <a class="nav-link {{ Route::current()->getName() != 'orders.overall' ? 'collapsed' : '' }}" href="/orders/overall">
                <i class="bi bi-inboxes"></i>
                <span>Overall List</span>
            </a>
        </li><!-- End Overall Order Nav -->

        <li class="nav-item">
            <a class="nav-link {{ Route::current()->getName() != 'orders.pending' ? 'collapsed' : '' }}" href="/orders/pending">
                <i class="bi bi-clock-history"></i>
                <span>Pending List</span>
            </a>
        </li><!-- End Pending List Nav -->

        <li class="nav-item">
            <a class="nav-link  {{ Route::current()->getName() != 'buckets.index' ? 'collapsed' : '' }}" href="/buckets">
                <i class="bi bi-basket"></i>
                <span>Bucket List</span>
            </a>
        </li><!-- End Bucket List Nav -->

        <li class="nav-item">
            <a class="nav-link {{ Route::current()->getName() != 'orders.packing' ? 'collapsed' : '' }}" href="/orders/packing">
                <i class="bi bi-box-seam"></i>
                <span>Packing List</span>
            </a>
        </li><!-- End Packing List Nav -->

        <li class="nav-item">
            <a class="nav-link {{ Route::current()->getName() != 'orders.readyToShip' ? 'collapsed' : '' }}" href="/orders/ready-to-ship">
                <i class="bi bi-box-seam"></i>
                <span>Pending Shipping List</span>
            </a>
        </li><!-- End Packing List Nav -->

        <li class="nav-item">
            <a class="nav-link {{ Route::current()->getName() != 'orders.shipping' ? 'collapsed' : '' }}" href="/orders/shipping">
                <i class="bi bi-truck"></i>
                <span>Shipping List</span>
            </a>
        </li><!-- End Shipping List Nav -->

        <li class="nav-item">
            <a class="nav-link {{ Route::current()->getName() != 'orders.delivered' ? 'collapsed' : '' }}" href="/orders/delivered">
                <i class="bi bi-check-circle"></i>
                <span>Delivered List</span>
            </a>
        </li><!-- End delivered List Nav -->

        <li class="nav-item">
            <a class="nav-link {{ Route::current()->getName() != 'orders.returned' ? 'collapsed' : '' }}" href="/orders/returned">
                <i class="bi bi-file-x"></i>
                <span>Return List</span>
            </a>
        </li><!-- End return List Nav -->

        <!-- Scan Parcel link -->
        <li class="nav-item">
            <a class="nav-link {{ Route::current()->getName() != 'orders.scan' ? 'collapsed' : '' }}" href="/orders/scan">
                <i class="bx bx-barcode-reader"></i>
                <span>Scan Parcel</span>
            </a>


        <li class="nav-item">
            <a class="nav-link collapsed" href="users-profile.html">
                <i class="bi bi-person"></i>
                <span>Profile</span>
            </a>
        </li><!-- End Profile Page Nav -->

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
