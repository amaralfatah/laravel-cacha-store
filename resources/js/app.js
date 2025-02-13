// resources/js/app.js

import './bootstrap';
import $ from 'jquery';  // Mengimpor jQuery
import DataTable from 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.css';

// Tidak perlu menulis ke `window`, jQuery dan DataTable sudah dapat digunakan langsung
$(document).ready(function() {
    // Inisialisasi DataTable
    $('.datatable').DataTable(); // Sesuaikan dengan selector Anda
});
