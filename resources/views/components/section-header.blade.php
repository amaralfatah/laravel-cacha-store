@props([
    'title',
    'route' => null,
    'buttonText' => null,
    'icon' => 'bx-plus'
])

<div>
    {{-- Breadcrumb Section --}}
    <div class="mb-3">
        @php
            $segments = request()->segments();
            $currentUrl = '';
        @endphp

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item">
                    <a href="{{ url('/') }}" class="text-decoration-none d-flex align-items-center">
                        <i class="bx bx-home-alt fs-5"></i>
                    </a>
                </li>
                @foreach($segments as $key => $segment)
                    @php
                        $currentUrl .= '/' . $segment;
                        $isLast = $loop->last;
                        $segmentTitle = ucwords(str_replace(['-', '_'], ' ', $segment));
                    @endphp
                    <li class="breadcrumb-item {{ $isLast ? 'active' : '' }} d-flex align-items-center"
                        {!! $isLast ? 'aria-current="page"' : '' !!}>
                        @if($isLast)
                            {{ $segmentTitle }}
                        @else
                            <a href="{{ $currentUrl }}" class="text-decoration-none">
                                {{ $segmentTitle }}
                            </a>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    </div>

    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">{{ $title }}</h4>
        <div class="d-flex gap-2">
            {{ $actions ?? '' }}
            @if($route && $buttonText)
                <a href="{{ $route }}" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="bx {{ $icon }}"></i>
                    <span>{{ $buttonText }}</span>
                </a>
            @endif
        </div>
    </div>
</div>

{{-- Basic usage --}}
{{--<x-section-header--}}
{{--    title="Manajemen Pengguna"--}}
{{--    :route="route('users.create')"--}}
{{--    buttonText="Tambah User"--}}
{{--    icon="bx-plus"--}}
{{--/>--}}

{{-- With custom actions --}}
{{--<x-section-header title="Manajemen Pengguna">--}}
{{--    <x-slot:actions>--}}
{{--        <a href="{{ route('users.create') }}" class="btn btn-primary d-flex align-items-center gap-1">--}}
{{--            <i class="bx bx-plus"></i>--}}
{{--            <span>Tambah User</span>--}}
{{--        </a>--}}
{{--    </x-slot:actions>--}}
{{--</x-section-header>--}}
