@props(['title' => 'Untitled'])

    <!-- ======= HTML Header ======= -->
    <x-header :title="$title" />

    <!-- ======= Sidebar ======= -->
    <x-sidebar />

    <main id="main" class="main" style="min-height: 100vh">

        <x-breadcrumb :title="$title" />

        <!-- ======= Notification Alert ======= -->
        <x-notification-alert />


        {{ $slot }}

    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <x-footer :script="$script" />

