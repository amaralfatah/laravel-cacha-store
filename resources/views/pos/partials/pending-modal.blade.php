<div class="modal fade" id="pendingTransactionsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class='bx bx-time-five me-2'></i>
                    Transaksi Tertunda
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>



            <div class="modal-body">

                <!-- Search bar untuk transaksi tertunda -->
                <div class=" py-3 border-bottom">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text">
                            <i class='bx bx-search'></i>
                        </span>
                        <input type="text" id="pending-search" class="form-control"
                            placeholder="Cari berdasarkan faktur atau pelanggan...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="pending-transactions-table">
                        <thead class="table-light">
                            <tr>
                                <th>Faktur</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
