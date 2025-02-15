import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.css';

// Export fungsi inisialisasi untuk digunakan di komponen lain
export function initializeDataTable(selector, options = {}) {
    return new DataTable(selector, {
        processing: true,
        serverSide: true,
        ...options
    });
}

// Inisialisasi default untuk semua tabel dengan class 'datatable'
document.addEventListener('DOMContentLoaded', () => {
    $('.datatable').each(function() {
        if (!$.fn.DataTable.isDataTable(this)) {
            $(this).DataTable({
                processing: true,
                serverSide: true,
            });
        }
    });
});
