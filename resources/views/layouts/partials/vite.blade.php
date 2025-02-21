@php
    $isProduction = app()->environment('production');
    $manifestPath = public_path('build/manifest.json');
@endphp

@if (file_exists($manifestPath))
    @php
        $manifest = json_decode(file_get_contents($manifestPath), true);
    @endphp
    <link rel="stylesheet" href="{{ asset('build/'.$manifest['resources/css/app.css']['file']) }}">
    <script type="module" src="{{ asset('build/'.$manifest['resources/js/app.js']['file']) }}"></script>
    @if (isset($manifest['resources/js/app.js']['css']))
        @foreach ($manifest['resources/js/app.js']['css'] as $css)
            <link rel="stylesheet" href="{{ asset('build/'.$css) }}">
        @endforeach
    @endif
@else
    @viteReactRefresh
    @vite(['resources/js/app.js', 'resources/css/app.css'])
@endif
