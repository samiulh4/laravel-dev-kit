<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-skin="default"
    data-assets-path="{{ asset('assets/admin').'/' }}" data-template="vertical-menu-template" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />

    <title>@yield('title', 'Laravel Dev Kit | Admin')</title>

    <!-- Canonical SEO -->
    <meta name="description" content="Laravel 12 and Bootstrap 5 admin for responsive web apps." />
    <meta name="keywords" content="laravel 12, bootstrap 5" />

    <!-- Open Graph (OG) meta tags -->
    @include('partials.admin.open-grap-meta-tags')

    <link rel="canonical" href="https://themeselection.com/item/sneat-dashboard-pro-bootstrap/" />

    <!-- Google Tag Manager -->
    {{-- @include('partials.admin.google-tag-manager') --}}

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/admin/img/favicon/favicon.ico') }}" />

    @include('partials.admin.css')
    @yield('style')

    @include('partials.admin.head-js')

</head>

<body>

    @include('partials.admin.no-script')

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">

        <div class="layout-container">

            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu">

                @include('partials.admin.sidebar-brand')

                <div class="menu-inner-shadow"></div>

                @include('partials.admin.sidebar-menu')

            </aside>

            @include('partials.admin.sidebar-menu-mobile-toggler')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">

                <!-- Navbar -->
                @include('partials.admin.navbar')
                <!-- / Navbar -->


                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">

                        @yield('content')

                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    @include('partials.admin.footer')
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>

    </div>
    <!-- / Layout wrapper -->

    {{-- @include('partials.admin.buy-now') --}}

    @include('partials.admin.js')
    @yield('script')
</body>

</html>
