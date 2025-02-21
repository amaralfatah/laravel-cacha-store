@if(app()->environment('local'))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@else
    <!-- File CSS dan JS yang sudah di-build -->
    <link rel="stylesheet" href="{{ asset('build/assets/app-wIOcQJrJ.css') }}">
    <link rel="stylesheet" href="{{ asset('build/assets/app-DcDb03eT.css') }}">
    <script src="{{ asset('build/assets/app-BKFiQ_FA.js') }}" defer></script>
@endif
