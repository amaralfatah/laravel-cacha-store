@php
    $segments = request()->segments();
    $currentUrl = '';
@endphp

@if(!empty($segments))
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            {{-- Home Link --}}
            <li class="breadcrumb-item">
                <a href="{{ url('/') }}" class="text-decoration-none">
                    <i class="fas fa-home"></i> Home
                </a>
            </li>

            {{-- Dynamic Segments --}}
            @foreach($segments as $key => $segment)
                @php
                    $currentUrl .= '/' . $segment;
                    $isLast = $loop->last;
                    $title = ucwords(str_replace(['-', '_'], ' ', $segment));
                @endphp

                <li class="breadcrumb-item {{ $isLast ? 'active fw-bold' : '' }}"
                    {!! $isLast ? 'aria-current="page"' : '' !!}>
                    @if($isLast)
                        {{ $title }}
                    @else
                        <a href="{{ $currentUrl }}" class="text-decoration-none">
                            {{ $title }}
                        </a>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
