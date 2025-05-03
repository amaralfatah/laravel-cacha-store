{{-- categories/partials/status.blade.php --}}
<span class="badge {{ $category->is_active ? 'bg-success' : 'bg-danger' }}">
    {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
</span>
