{{-- resources/views/components/toast.blade.php --}}
@if (session()->has('success') || session()->has('error') || session()->has('warning') || session()->has('info'))
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        @if (session()->has('success'))
            <div class="bs-toast toast fade show bg-success" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="bx bx-check me-2"></i>
                    <div class="me-auto fw-semibold">Success</div>
                    <small>Just now</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
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
                    <div class="me-auto fw-semibold">Error</div>
                    <small>Just now</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
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
                    <div class="me-auto fw-semibold">Warning</div>
                    <small>Just now</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
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
                    <div class="me-auto fw-semibold">Information</div>
                    <small>Just now</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body text-white">
                    {{ session('info') }}
                </div>
            </div>
        @endif
    </div>
@endif



{{-- return redirect()->route('home')->with('success', 'Operation completed successfully!');
return redirect()->back()->with('error', 'Something went wrong!');
return redirect()->route('dashboard')->with('warning', 'Please complete your profile.');
return redirect()->route('settings')->with('info', 'New features available!'); --}}

<script>
    // Toast initialization
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
