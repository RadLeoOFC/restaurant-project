<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column min-vh-100 bg-light" style="overflow-x: hidden;">

    <!-- Mobile Navbar -->
    <nav class="navbar navbar-light bg-light d-lg-none w-100">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Menu</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <!-- Offcanvas Sidebar for Mobile -->
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileSidebar">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Menu</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            @include('layouts.sidebar')
        </div>
    </div>

    <!-- Main Layout -->
    <div class="container-fluid p-0 flex-grow-1">
        <div class="row flex-nowrap w-100 m-0">
            <!-- Sidebar Desktop -->
            <div class="col-lg-2 d-none d-lg-block p-0 bg-white border-end">
                @include('layouts.sidebar')
            </div>

            <!-- Main Content Area -->
            <div class="col-12 col-lg-10 d-flex flex-column p-0">

                <!-- Header -->
                @include('layouts.navigation')

                @isset($header)
                    <header class="bg-white shadow-sm w-100 mb-3">
                        <div class="px-3">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Main Page Content -->
                <main class="flex-grow-1 w-100 px-3 py-4">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <!-- Footer -->
    @include('layouts.footer')

    <!-- Bootstrap & JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Init dropdowns -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var dropdownTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
            dropdownTriggerList.map(function (el) {
                return new bootstrap.Dropdown(el);
            });
        });
    </script>

    <script>
        setInterval(() => {
            fetch('/run-desk-statuses');
        }, 60000);
    </script>

    <!-- jQuery (обязательно ДО кастомных скриптов) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @stack('scripts')

    @yield('scripts')


</body>
</html>
