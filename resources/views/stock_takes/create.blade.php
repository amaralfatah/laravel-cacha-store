@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">New Stock Take</h2>

        <form action="{{ route('stock-takes.store') }}" method="POST">
            @csrf
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date"
                                       value="{{ old('date', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text"
                                   id="searchInput"
                                   class="form-control"
                                   placeholder="Search product...">
                        </div>
                        <div class="col-md-3">
                            <select id="categoryFilter" class="form-control">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="showZeroStock">
                                <label class="form-check-label" for="showZeroStock">
                                    Show Zero Stock Only
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Table with fixed header -->
                    <div style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-striped" id="products-table">
                            <thead style="position: sticky; top: 0; background: white;">
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>Current Stock</th>
                                <th>Actual Stock</th>
                                <th>Scan</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($products as $product)
                                @foreach($product->productUnits as $productUnit)
                                    <tr class="product-row"
                                        data-product="{{ strtolower($product->name) }}"
                                        data-category="{{ $product->category_id }}"
                                        data-stock="{{ $currentStock = $product->inventories->where('unit_id', $productUnit->unit_id)->first()?->quantity ?? 0 }}">
                                        <td>
                                            {{ $product->name }}
                                            @if($product->barcode)
                                                <br><small class="text-muted">{{ $product->barcode }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $product->category->name }}</td>
                                        <td>
                                            {{ $productUnit->unit->name }}
                                            <input type="hidden"
                                                   name="items[{{ $loop->parent->index }}_{{ $loop->index }}][product_id]"
                                                   value="{{ $product->id }}">
                                            <input type="hidden"
                                                   name="items[{{ $loop->parent->index }}_{{ $loop->index }}][unit_id]"
                                                   value="{{ $productUnit->unit_id }}">
                                        </td>
                                        <td>{{ number_format($currentStock, 2) }}</td>
                                        <td>
                                            <input type="number"
                                                   name="items[{{ $loop->parent->index }}_{{ $loop->index }}][actual_qty]"
                                                   class="form-control actual-qty"
                                                   step="0.01"
                                                   required>
                                        </td>
                                        <td>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary scan-btn"
                                                    data-row-index="{{ $loop->parent->index }}_{{ $loop->index }}">
                                                <i class="bi bi-upc-scan"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Save Stock Take</button>
                <a href="{{ route('stock-takes.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <!-- Barcode Scanner Modal -->
    <div class="modal fade" id="scannerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Scan Barcode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text"
                           id="barcodeInput"
                           class="form-control"
                           placeholder="Scan or type barcode"
                           autofocus>
                    <input type="hidden" id="currentRowIndex">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Search functionality
            $('#searchInput').on('keyup', function() {
                let value = $(this).val().toLowerCase();
                filterProducts();
            });

            // Category filter
            $('#categoryFilter').on('change', function() {
                filterProducts();
            });

            // Zero stock filter
            $('#showZeroStock').on('change', function() {
                filterProducts();
            });

            function filterProducts() {
                let searchValue = $('#searchInput').val().toLowerCase();
                let categoryValue = $('#categoryFilter').val();
                let showZeroStock = $('#showZeroStock').is(':checked');

                $('.product-row').each(function() {
                    let productName = $(this).data('product');
                    let category = $(this).data('category');
                    let stock = parseFloat($(this).data('stock'));

                    let showBySearch = productName.includes(searchValue);
                    let showByCategory = !categoryValue || category == categoryValue;
                    let showByStock = !showZeroStock || stock === 0;

                    $(this).toggle(showBySearch && showByCategory && showByStock);
                });
            }

            // Barcode scanning
            $('.scan-btn').on('click', function() {
                $('#currentRowIndex').val($(this).data('row-index'));
                $('#scannerModal').modal('show');
                setTimeout(() => $('#barcodeInput').focus(), 500);
            });

            $('#barcodeInput').on('keyup', function(e) {
                if(e.key === 'Enter') {
                    let barcode = $(this).val();
                    let rowIndex = $('#currentRowIndex').val();

                    // Find product by barcode
                    let found = false;
                    $('.product-row').each(function() {
                        if($(this).find('.barcode-text').text() === barcode) {
                            // Auto-fill quantity (example: set to 1)
                            $(`input[name="items[${rowIndex}][actual_qty]"]`).val(1);
                            found = true;
                            return false;
                        }
                    });

                    if(!found) {
                        alert('Product not found!');
                    }

                    // Clear and close
                    $(this).val('');
                    $('#scannerModal').modal('hide');
                }
            });

            // Auto-scroll to input when clicking quantity field
            $('.actual-qty').on('focus', function() {
                let container = $(this).closest('.card-body');
                let scrollTo = $(this).closest('tr');

                container.animate({
                    scrollTop: scrollTo.offset().top - container.offset().top + container.scrollTop()
                });
            });
        });
    </script>
@endpush
