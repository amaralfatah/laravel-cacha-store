{{-- taxes/partials/status.blade.php --}}
<span class="badge {{ $tax->is_active ? 'bg-success' : 'bg-danger' }}">
    {{ $tax->is_active ? 'Aktif' : 'Nonaktif' }}
</span>
