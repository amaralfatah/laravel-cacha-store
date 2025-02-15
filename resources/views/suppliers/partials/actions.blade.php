<div class="d-flex gap-1">
    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-info">
        <i class="bi bi-pencil"></i> Edit
    </a>
    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline delete-form">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">
            <i class="bi bi-trash"></i> Delete
        </button>
    </form>
</div>
