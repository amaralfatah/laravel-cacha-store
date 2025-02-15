{{-- resources/views/reports/partials/units-column.blade.php --}}
@foreach($units as $unit)
    <div class="mb-1">
        {{ $unit->unit->name }}: {{ $unit->stock }}
        @if($unit->stock <= $unit->min_stock)
            <span class="badge bg-danger">Stok Menipis</span>
        @endif
    </div>
@endforeach
