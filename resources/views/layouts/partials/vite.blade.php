@if(app()->environment('local'))
    @vite(['resources/css/app.css', 'resources/js/app.js'])

@endif
