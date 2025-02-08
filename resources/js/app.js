import './bootstrap';

// resources/js/app.js
document.addEventListener('DOMContentLoaded', function () {
    const toasts = document.querySelectorAll('.bs-toast');
    toasts.forEach(toastEl => {
        const toast = new bootstrap.Toast(toastEl, {
            delay: 5000, // Auto hide after 5 seconds
            animation: true
        });
        toast.show();
    });
});
