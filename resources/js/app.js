import './bootstrap';

// resources/js/app.js
document.addEventListener('DOMContentLoaded', function () {
    const toastElList = document.querySelectorAll('.bs-toast');
    toastElList.forEach((toastEl) => {
        // Inisialisasi toast
        const toast = new bootstrap.Toast(toastEl, {
            autohide: true,
            delay: 5000
        });

        // Show the toast
        toast.show();

        // Tambahkan event listener untuk tombol close
        const closeBtn = toastEl.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                toast.hide();
            });
        }

        // Otomatis hapus element setelah hidden
        toastEl.addEventListener('hidden.bs.toast', () => {
            toastEl.remove();
        });
    });
});
