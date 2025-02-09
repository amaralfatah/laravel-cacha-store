// resources/js/app.js

import './bootstrap';
import jQuery from 'jquery';
import DataTable from 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.css';

// Set jQuery globally
window.$ = window.jQuery = jQuery;

// Initialize DataTables
window.DataTable = DataTable(window, $);

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
