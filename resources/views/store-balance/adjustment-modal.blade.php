{{-- resources/views/admin/store-balance/balance-adjustment-modal.blade.php --}}
<div class="modal fade" id="adjustmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('stores.balance.adjustment', $store) }}" method="POST" id="adjustmentForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Balance Adjustment - {{ $store->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="type" id="typeCash" value="cash" checked>
                            <label class="btn btn-outline-primary" for="typeCash">
                                <i class='bx bx-money me-1'></i> Cash
                            </label>
                            <input type="radio" class="btn-check" name="type" id="typeTransfer" value="transfer">
                            <label class="btn btn-outline-primary" for="typeTransfer">
                                <i class='bx bx-transfer me-1'></i> Transfer
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adjustment Type</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="adjustment_type" id="typeIn" value="in" checked>
                            <label class="btn btn-outline-success" for="typeIn">
                                <i class='bx bx-plus-circle'></i> Money In
                            </label>
                            <input type="radio" class="btn-check" name="adjustment_type" id="typeOut" value="out">
                            <label class="btn btn-outline-danger" for="typeOut">
                                <i class='bx bx-minus-circle'></i> Money Out
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="amount">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   id="amount"
                                   name="amount"
                                   min="0"
                                   step="0.01"
                                   required
                                   value="{{ old('amount') }}">
                            @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="notes">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes"
                                  name="notes"
                                  rows="3"
                                  required>{{ old('notes') }}</textarea>
                        @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->any() || session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = new bootstrap.Modal(document.getElementById('adjustmentModal'));
            modal.show();
        });
    </script>
@endif
