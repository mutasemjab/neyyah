@php
    $locale = app()->getLocale();
    $dir = $locale === 'ar' ? 'rtl' : 'ltr';
@endphp
<!doctype html>
<html lang="{{ $locale }}" dir="{{ $dir }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'graphco')</title>

    <!-- Section: Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Tajawal:wght@400;500;700&display=swap"
        rel="stylesheet">

    <!-- Section: Styles -->
    <link rel="stylesheet" href="{{ asset('assets_front/css/base.css') }}">
    @if ($dir === 'rtl')
        <link rel="stylesheet" href="{{ asset('assets_front/css/rtl.css') }}">
    @endif
</head>

<body>
    @include('user.includes.header')

    <main class="page-wrap">
        @yield('content')
    </main>

    @include('user.includes.footer')

    <!-- Section: Scripts -->
    <script src="{{ asset('assets_front/js/app.js') }}"></script>
    @stack('scripts')
    @yield('script')
</body>

</html>
