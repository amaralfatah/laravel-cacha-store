@php
    $segments = request()->segments();
@endphp

@if(!empty($segments) && !in_array($segments[0], ['dashboard']))
    <h4 class="fw-bold py-3 mb-4">
        @foreach($segments as $segment)
            @if($loop->last)
                {{ ucwords(str_replace('-', ' ', $segment)) }}
            @else
                <span class="text-muted fw-light">{{ ucwords(str_replace('-', ' ', $segment)) }} /</span>
            @endif
        @endforeach
    </h4>
@endif
