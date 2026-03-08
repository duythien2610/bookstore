<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Modtra Books') — Modtra Books</title>
    <meta name="description" content="@yield('meta_description', 'Khám phá thế giới tri thức cùng Modtra Books — Nhà sách trực tuyến hàng đầu.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/modtra.css') }}">
    @stack('styles')
</head>
<body>
    @include('partials._header')

    <main id="main-content">
        @yield('content')
    </main>

    @include('partials._footer')

    @include('partials._chatbot')

    @stack('scripts')
</body>
</html>
