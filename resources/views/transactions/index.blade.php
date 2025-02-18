@extends('layouts.app')

@section('content')
    <x-section-header
        title="Daftar Transaksi"
    />

    <div class="card mb-4">
        <div class="card-body">
            <div class="filter-row">
                <div class="row g-3">
                    @if(auth()->user()->role === 'admin')
                        <div class="col-md-3">
                            <label class="form-label">Toko</label>
                            <select id="store-filter" class="form-select">
                                <option value="">Semua Toko</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <label class="form-label">Status Transaksi</label>
                        <select id="status-filter" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending">Draft</option>
                            <option value="success">Selesai</option>
                            <option value="failed">Gagal</option>
                            <option value="cancelled">Dibatalkan</option>
                            <option value="returned">Dikembalikan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Periode</label>
                        <select id="period-filter" class="form-select">
                            <option value="">Semua Periode</option>
                            <option value="today">Hari Ini</option>
                            <option value="yesterday">Kemarin</option>
                            <option value="this_week">Minggu Ini</option>
                            <option value="this_month">Bulan Ini</option>
                            <option value="last_month">Bulan Lalu</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button id="reset-filter" class="btn btn-secondary">
                            Reset Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="transactions-table">
                    <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>Tanggal</th>
                        @if(auth()->user()->role === 'admin')
                            <th>Toko</th>
                        @endif
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="returnModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pengembalian Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="returnForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Anda yakin ingin mengembalikan transaksi ini?</p>
                        <div class="mb-3">
                            <label class="form-label">Alasan Pengembalian</label>
                            <textarea name="reason" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan Tambahan</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Kembalikan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = $('#transactions-table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: "{{ route('transactions.index') }}",
                    data: function(d) {
                        d.status = $('#status-filter').val();
                        d.period = $('#period-filter').val();
                    }
                },
                columns: [
                    {
                        data: 'invoice_number',
                        name: 'invoice_number',
                        width: '120px'
                    },
                    {
                        data: 'invoice_date_formatted',
                        name: 'invoice_date',
                        width: '150px'
                    },
                        @if(auth()->user()->role === 'admin')
                    {
                        data: 'store_name',
                        name: 'store.name'
                    },
                        @endif
                    {
                        data: 'customer_name',
                        name: 'customer.name',
                        width: '200px'
                    },
                    {
                        data: 'final_amount_formatted',
                        name: 'final_amount',
                        width: '120px',
                        className: 'text-end'
                    },
                    {
                        data: 'status_formatted',
                        name: 'status',
                        width: '100px'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '100px'
                    }
                ],
                order: [[1, 'desc']],
            });

            // Event listener untuk filter
            $('#status-filter, #period-filter').change(function() {
                table.draw();
            });

            // Reset filter
            $('#reset-filter').click(function() {
                $('#status-filter, #period-filter').val('');
                table.draw();
            });

            // Tambahkan tooltip untuk cell yang terpotong
            $('#transactions-table').on('mouseenter', 'td', function() {
                if(this.offsetWidth < this.scrollWidth) {
                    $(this).attr('title', $(this).text());
                }
            });



            const returnForm = document.getElementById('returnForm');
            returnForm.addEventListener('submit', function(e) {
                e.preventDefault();

                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        // Gunakan value dari input hidden _token
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        reason: this.querySelector('[name="reason"]').value,
                        notes: this.querySelector('[name="notes"]').value
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        // Sembunyikan modal
                        const returnModal = document.getElementById('returnModal');
                        bootstrap.Modal.getInstance(returnModal).hide();

                        // Reset form
                        this.reset();

                        // Tampilkan notifikasi sederhana
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-success alert-dismissible fade show';
                        alert.innerHTML = `
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                        document.querySelector('.card-body').prepend(alert);

                        // Reload table
                        table.ajax.reload();

                        // Hapus alert setelah 3 detik
                        setTimeout(() => alert.remove(), 3000);
                    })
                    .catch(error => {
                        const message = error.response?.data?.message || 'Terjadi kesalahan';
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-danger alert-dismissible fade show';
                        alert.innerHTML = `
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                        document.querySelector('.modal-body').prepend(alert);
                    });
            });
        });

        // Fungsi global untuk membuka modal return
        function returnTransaction(id) {
            const returnModal = document.getElementById('returnModal');
            const form = document.getElementById('returnForm');
            form.action = `/transactions/${id}/return`;

            // Bersihkan alert error sebelumnya jika ada
            const existingAlert = form.querySelector('.alert');
            if (existingAlert) {
                existingAlert.remove();
            }

            // Reset form
            form.reset();

            // Tampilkan modal
            const modal = new bootstrap.Modal(returnModal);
            modal.show();
        }


    </script>
@endpush
