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

<div class="{{ $columnSize }}">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div class="card-info">
                    <p class="card-text">{{ $title }}</p>
                    <div class="d-flex align-items-end mb-2">
                        <h4 class="card-title mb-0 me-2">
                            @if($format === 'currency')
                                Rp {{ number_format($value, $decimals, ',', '.') }}
                            @else
                                {{ $value }}
                            @endif
                        </h4>
                        @if(isset($growth))
                            @if($growth > 0)
                                <small class="text-success">(+{{ number_format($growth, 1) }}%)</small>
                            @elseif($growth < 0)
                                <small class="text-danger">({{ number_format($growth, 1) }}%)</small>
                            @else
                                <small class="text-muted">(0%)</small>
                            @endif
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
