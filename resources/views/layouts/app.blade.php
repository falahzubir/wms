@props(['title' => 'Untitled', 'crumbList' => false])
    <!-- ======= HTML Header ======= -->
    <x-header :title="$title" />

    <!-- ======= Sidebar ======= -->
    <x-sidebar />

    <main id="main" class="main" style="min-height: 100vh">

        <x-breadcrumb :title="$title" :crumbList="$crumbList" />

        <!-- ======= Notification Alert ======= -->
        @if(request()->segment(1) === 'alternative_postcode')
            <x-alternative-postcode-alert />
        @else
            <x-notification-alert />
        @endif


        {{ $slot }}

    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <x-footer :script="$script" />

