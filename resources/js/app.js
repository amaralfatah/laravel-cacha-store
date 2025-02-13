// resources/js/app.js

import './bootstrap';
import jQuery from 'jquery';
import DataTable from 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.css';

// Set jQuery globally
window.$ = window.jQuery = jQuery;

// Initialize DataTables
window.DataTable = DataTable(window, $);

