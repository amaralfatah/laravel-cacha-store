<div class="d-flex gap-2">
    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-info">
        <i class="bx bx-edit"></i>
    </a>
    @if($user->id !== auth()->id())
        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline delete-form">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger"
                    onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                <i class="bx bx-trash"></i>
            </button>
        </form>
    @endif
</div>
