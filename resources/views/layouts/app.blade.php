@props(['title' => 'Untitled'])
    <!-- ======= HTML Header ======= -->
    <x-header :title="$title" />

    <!-- ======= Sidebar ======= -->
    <x-sidebar />

    <main id="main" class="main">

        <x-breadcrumb :title="$title" />

        {{ $slot }}

    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <x-footer :script="$script" />
