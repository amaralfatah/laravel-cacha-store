<!-- resources/views/components/card-status.blade.php -->

@props([
    'title' => '',
    'value' => 0,
    'growth' => null,
    'subtitle' => '',
    'icon' => 'bx-calendar',
    'iconColor' => 'primary',
    'format' => 'currency',
    'decimals' => 0,
    'columnSize' => 'col-lg-3 col-md-6 col-sm-6'
])

@php
    // Format the display value
    $formattedValue = $value;

    if ($format === 'currency') {
        if ($value >= 1000000000) {
            // For billions (miliar) - 2 decimal places
            $formattedValue = 'Rp ' . number_format($value / 1000000000, 2, ',', '.') . ' M';
        } elseif ($value >= 1000000) {
            // For millions (juta) - 2 decimal places
            $formattedValue = 'Rp ' . number_format($value / 1000000, 2, ',', '.') . ' Jt';
        } else {
            // No abbreviation and use specified decimals for values under 1 million
            $formattedValue = 'Rp ' . number_format($value, $decimals, ',', '.');
        }
    } else {
        // For non-currency values
        if ($value >= 1000000000) {
            // 2 decimal places for billions
            $formattedValue = number_format($value / 1000000000, 2, ',', '.') . ' M';
        } elseif ($value >= 1000000) {
            // 2 decimal places for millions
            $formattedValue = number_format($value / 1000000, 2, ',', '.') . ' Jt';
        } else {
            // Use specified decimals for smaller values
            $formattedValue = number_format($value, $decimals, ',', '.');
        }
    }

    // Format growth display
    $formattedGrowth = '';
    if (isset($growth)) {
        if ($growth > 0) {
            $formattedGrowth = '<small class="text-success">(+' . number_format($growth, $decimals) . '%)</small>';
        } elseif ($growth < 0) {
            $formattedGrowth = '<small class="text-danger">(' . number_format($growth, $decimals) . '%)</small>';
        } else {
            $formattedGrowth = '<small class="text-muted">(0%)</small>';
        }
    }
@endphp

<div class="{{ $columnSize }}">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div class="card-info">
                    <p class="card-text">{{ $title }}</p>
                    <div class="d-flex align-items-end mb-2">
                        <h4 class="card-title mb-0 me-2">
                            {{ $formattedValue }}
                        </h4>
                        @if(isset($growth))
                            {!! $formattedGrowth !!}
                        @endif
                    </div>
                    <small class="text-muted">{{ $subtitle }}</small>
                </div>
                <div class="card-icon">
                    <span class="badge bg-label-{{ $iconColor }} rounded p-2">
                        <i class="bx {{ $icon }} bx-sm"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
