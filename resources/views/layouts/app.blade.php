@props(['title' => 'Untitled'])
<div id="wms">

    <!-- ======= HTML Header ======= -->
    <x-header :title="$title" />

    <!-- ======= Sidebar ======= -->
    <x-sidebar />

    <main id="main" class="main">

        <x-breadcrumb :title="$title" />

        <!-- ======= Notification Alert ======= -->
        <x-notification-alert />


        {{ $slot }}

    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <x-footer :script="$script" />

</div>
