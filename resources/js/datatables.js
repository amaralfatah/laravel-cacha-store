// resources/js/datatables.js

import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.css';
import 'datatables.net-responsive-bs5/css/responsive.bootstrap5.css';

// Extend jQuery DataTable defaults
$.extend(true, $.fn.dataTable.defaults, {
    processing: true,
    serverSide: true,
    scrollX: true,
    dom:
        "<'row mb-2'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex justify-content-end'f<'ms-3'B>>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row mt-2'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-end'p>>",
    buttons: [
        {
            extend: 'excel',
            text: '<i class="bx bxs-file-export"></i>',
            className: 'btn btn-outline-secondary btn-sm'
        },
        {
            extend: 'pdf',
            text: '<i class="bx bxs-file-pdf" ></i>',
            className: 'btn btn-outline-secondary btn-sm'
        },
        {
            extend: 'print',
            text: '<i class="tf-icons bx bx-printer me-1"></i>',
            className: 'btn btn-outline-secondary btn-sm'
        }
    ],
});
