<div class="d-flex gap-1">
    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info">
        Lihat
    </a>
    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">
        Edit
    </a>
    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah anda yakin?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">
            Hapus
        </button>
    </form>
</div>
