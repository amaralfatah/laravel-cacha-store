{{-- discounts/partials/actions.blade.php --}}
<div class="btn-group" role="group">
    <a href="{{ route('discounts.edit', $discount) }}"
       class="btn btn-sm btn-info"
       data-bs-toggle="tooltip"
       title="Edit">
        <i class='bx bx-edit'></i>
    </a>

    <form action="{{ route('discounts.destroy', $discount) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="btn btn-sm btn-danger"
                onclick="return confirm('Apakah Anda yakin ingin menghapus diskon ini?')"
                data-bs-toggle="tooltip"
                title="Hapus">
            <i class='bx bx-trash'></i>
        </button>
    </form>
</div>
