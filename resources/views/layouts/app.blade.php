<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-light d-flex flex-column min-vh-100">

    <div class="container-fluid flex-grow-1">
        <div class="row g-0 flex-grow-1">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 bg-white border-end">
                @include('layouts.sidebar')
            </div>

            <!-- Page Content -->
            <div class="col-md-9 col-lg-10 d-flex flex-column">
                @include('layouts.navigation')

                @isset($header)
                    <header class="bg-white shadow-sm mb-3">
                        <div class="container-fluid px-0">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Main Content -->
                <main class="container-fluid flex-grow-1 py-4 px-0">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <!-- Footer -->
    @include('layouts.footer')

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Init Dropdowns -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var dropdownTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
            dropdownTriggerList.map(function (el) {
                return new bootstrap.Dropdown(el);
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
