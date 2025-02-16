<div class="d-flex gap-2">
    <a href="{{ route('groups.edit', $group) }}" class="btn btn-sm btn-info">
        <i class="bx bx-edit"></i>
    </a>
    <form action="{{ route('groups.destroy', $group) }}" method="POST" class="d-inline delete-form">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger"
                onclick="return confirm('Apakah Anda yakin ingin menghapus kelompok ini?')">
            <i class="bx bx-trash"></i>
        </button>
    </form>
</div>
