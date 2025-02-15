<div class="d-flex gap-1">
    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-info">Edit</a>
    <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger"
                onclick="return confirm('Are you sure?')">Delete</button>
    </form>
</div>
