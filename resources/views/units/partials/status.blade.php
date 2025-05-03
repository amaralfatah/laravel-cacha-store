{{-- units/partials/status.blade.php --}}
<span class="badge {{ $unit->is_active ? 'bg-success' : 'bg-danger' }}">
    {{ $unit->is_active ? 'Aktif' : 'Nonaktif' }}
</span>
