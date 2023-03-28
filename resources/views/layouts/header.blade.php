@props(['title'])

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>{{ config('app.name') }} - {{ $title }}</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicons -->
    <link href="{{ asset("assets/img/favicon.png") }}" rel="icon">
    <link href="{{ asset("assets/img/apple-touch-icon.png") }}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    {{-- Vite Processing --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- CDN CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset("assets/vendor/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet">
    <link href="{{ asset("assets/vendor/bootstrap-icons/bootstrap-icons.css") }}" rel="stylesheet">
    <link href="{{ asset("assets/vendor/boxicons/css/boxicons.min.css") }}" rel="stylesheet">
    <link href="{{ asset("assets/vendor/quill/quill.snow.css") }}" rel="stylesheet">
    <link href="{{ asset("assets/vendor/quill/quill.bubble.css") }}" rel="stylesheet">
    <link href="{{ asset("assets/vendor/remixicon/remixicon.css") }}" rel="stylesheet">
    <link href="{{ asset("assets/vendor/simple-datatables/style.css") }}" rel="stylesheet">
    <!-- Template Main CSS File -->
    <link href="{{ asset("assets/css/style.css?v=0.01") }}" rel="stylesheet">

    <!-- =======================================================
  * Template Name: NiceAdmin - v2.5.0
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

    <!-- ======= Header ======= -->
    <x-top-header />

    <!-- ======= Sidebar ======= -->
    <x-sidebar />
