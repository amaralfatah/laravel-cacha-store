{{-- resources/views/components/toast.blade.php --}}
@if (session()->has('success') || session()->has('error') || session()->has('warning') || session()->has('info'))
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        @if (session()->has('success'))
            <div class="bs-toast toast fade show bg-success" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="bx bx-check me-2"></i>
                    <div class="me-auto fw-semibold">Berhasil</div>
                    <small>Baru saja</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button>
                </div>
                <div class="toast-body text-white">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bs-toast toast fade show bg-danger" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="bx bx-error me-2"></i>
                    <div class="me-auto fw-semibold">Gagal</div>
                    <small>Baru saja</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button>
                </div>
                <div class="toast-body text-white">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if (session()->has('warning'))
            <div class="bs-toast toast fade show bg-warning" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="bx bx-warning me-2"></i>
                    <div class="me-auto fw-semibold">Peringatan</div>
                    <small>Baru saja</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button>
                </div>
                <div class="toast-body text-white">
                    {{ session('warning') }}
                </div>
            </div>
        @endif

        @if (session()->has('info'))
            <div class="bs-toast toast fade show bg-info" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="bx bx-info-circle me-2"></i>
                    <div class="me-auto fw-semibold">Informasi</div>
                    <small>Baru saja</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button>
                </div>
                <div class="toast-body text-white">
                    {{ session('info') }}
                </div>
            </div>
        @endif
    </div>
@endif

{{-- Contoh penggunaan:
return redirect()->route('home')->with('success', 'Operasi berhasil dilakukan!');
return redirect()->back()->with('error', 'Terjadi kesalahan!');
return redirect()->route('dashboard')->with('warning', 'Silakan lengkapi profil Anda.');
return redirect()->route('settings')->with('info', 'Fitur baru tersedia!'); --}}

<script>
    // Inisialisasi Toast
    document.addEventListener('DOMContentLoaded', function () {
        const toasts = document.querySelectorAll('.bs-toast');
        toasts.forEach(toastEl => {
            const toast = new bootstrap.Toast(toastEl, {
                delay: 5000,
                animation: true
            });
            toast.show();
        });
    });
</script>
