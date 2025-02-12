<!-- resources/views/product_units/index.blade.php -->
@extends('layouts.app')

@section('content')

    <div class="row mb-4">
        <div class="col-md-8">
            <h2>
                <i class="bi bi-box"></i> {{ $product->name }}
                <small class="text-muted">Unit Conversions</small>
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('products.units.create', $product) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Unit
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if(isset($productUnits) && ($defaultUnit = $productUnits->where('is_default', true)->first()))
        <div class="card mb-4 border-primary">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="card-title text-primary mb-0">
                            <i class="bi bi-star-fill"></i> Default Unit: {{ $defaultUnit->unit->name }}
                        </h5>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h4 class="mb-0">Rp {{ number_format($product->base_price, 0, ',', '.') }}</h4>
                        <small class="text-muted">Base price for all conversion calculations</small>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if($productUnits->isEmpty())
                <div class="text-center py-4">
                    <i class="bi bi-emoji-neutral display-4 text-muted"></i>
                    <p class="mt-3">No unit conversions found</p>
                    <a href="{{ route('products.units.create', $product) }}" class="btn btn-primary">
                        Add Your First Unit
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>Unit</th>
                            <th>Conversion</th>
                            <th>Price</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($productUnits as $productUnit)
                            <tr>
                                <td>
                                    <strong>{{ $productUnit->unit->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $productUnit->unit->code }}</small>
                                </td>
                                <td>
                                    {{ $productUnit->conversion_factor }}
                                    <small class="text-muted">x base unit</small>
                                </td>
                                <td>
                                    <strong>Rp {{ number_format($productUnit->price, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-center">
                                    @if($productUnit->is_default)
                                        <span class="badge bg-primary">Default Unit</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('products.units.edit', [$product, $productUnit]) }}"
                                       class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                    @if (!$productUnit->is_default)
                                        <form
                                            action="{{ route('products.units.destroy', [$product, $productUnit]) }}"
                                            method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    data-bs-toggle="tooltip" title="Delete Unit">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>


    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })

                // Delete confirmation
                const deleteForms = document.querySelectorAll('.delete-form')
                deleteForms.forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault()
                        if (confirm('Are you sure you want to delete this unit conversion?')) {
                            this.submit()
                        }
                    })
                })
            })
        </script>
    @endpush

    @push('styles')
        <style>
            .card {
                border-radius: 0.5rem;
            }

            .table th {
                font-weight: 600;
                text-transform: uppercase;
                font-size: 0.85rem;
            }

            .badge {
                font-weight: 500;
            }
        </style>
    @endpush
@endsection
