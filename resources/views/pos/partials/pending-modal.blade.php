<!-- Modal untuk Transaksi Tertunda - Windows Classic Style -->
<div class="modal fade" id="pendingTransactionsModal" tabindex="-1" aria-labelledby="pendingTransactionsModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border: 2px solid #919b9c; border-radius: 0; box-shadow: 3px 3px 5px rgba(0,0,0,0.3);">
            <div class="modal-header" style="background: linear-gradient(to bottom, #4f6acc 0%, #2a3c8e 100%); color: white; padding: 6px 10px; border-bottom: 1px solid #919b9c;">
                <h5 class="modal-title" id="pendingTransactionsModalLabel">
                    <i class='bx bx-time-five me-1'></i> Transaksi Tertunda
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background-color: #ece9d8; padding: 10px;">
                <div class="table-responsive">
                    <table class="table table-bordered" id="pending-transactions-table" style="border: 2px solid #919b9c;">
                        <thead style="background: linear-gradient(to bottom, #d1d7e6 0%, #b0b9d5 100%);">
                        <tr>
                            <th>No. Invoice</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Data will be loaded here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="background-color: #ece9d8; border-top: 1px solid #919b9c; padding: 8px;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="background: linear-gradient(to bottom, #f9f9f9 0%, #e0e0e0 100%); border: 2px solid #919b9c;">Tutup</button>
            </div>
        </div>
    </div>
</div>
