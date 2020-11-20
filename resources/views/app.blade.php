<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta name="developer" content="indra08031993@gmail.com">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="sipongi-menlhk.go.id">
    <meta name="description" content="">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/png" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <meta name="googlebot" content="noindex, nofollow, noarchive">

    <title>{{config('app.name')}}</title>

    {{-- <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons" rel="stylesheet"> --}}
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">

    <script>
        window.Laravel = {!! json_encode([
                'siteName' => config('app.name'),
                'siteUrl' => config('app.url'),
                'locale' => config('app.locale'),
                'fallbackLocale' => config('app.fallback_locale'),
                'apiUrl' => config('app.url').
                '/api'
            ]) !!};
    </script>
</head>

<body>
    <div id="app"></div>

    <script src="{{ mix('/js/app.js') }}"></script>
    <script src="{{ asset('/vendor/tinymce/tinymce.min.js') }}"></script>
</body>

</html>