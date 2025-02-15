@php
    $segments = request()->segments();
    // Filter out segments that are numeric (IDs)
    $filteredSegments = array_filter($segments, fn($segment) => !is_numeric($segment));
@endphp

@if(!empty($filteredSegments) && !in_array($filteredSegments[0], ['dashboard']))
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0 d-flex align-items-center">
            @foreach($filteredSegments as $segment)
                @if($loop->last)
                    {{ ucwords(str_replace('-', ' ', $segment)) }}
                @else
                    <span class="text-muted fw-light">{{ ucwords(str_replace('-', ' ', $segment)) }} /</span>
                @endif
            @endforeach
        </h4>

        <div class="d-flex align-items-center">
            {{ $slot ?? '' }}
        </div>
    </div>
@endif
