{{-- units/partials/actions.blade.php --}}
<div class="btn-group" role="group">
    <a href="{{ route('units.edit', $unit) }}"
       class="btn btn-sm btn-info"
       data-bs-toggle="tooltip"
       title="Edit">
        <i class='bx bx-edit'></i>
    </a>

    <form action="{{ route('units.destroy', $unit) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="btn btn-sm btn-danger"
                onclick="return confirm('Apakah Anda yakin ingin menghapus satuan ini?')"
                data-bs-toggle="tooltip"
                title="Hapus">
            <i class='bx bx-trash'></i>
        </button>
    </form>
</div>
