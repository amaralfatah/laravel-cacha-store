{{-- discounts/partials/status.blade.php --}}
<span class="badge {{ $discount->is_active ? 'bg-success' : 'bg-danger' }}">
    {{ $discount->is_active ? 'Aktif' : 'Nonaktif' }}
</span>
