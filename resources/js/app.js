import './bootstrap';
import $ from 'jquery';
import 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-buttons/js/buttons.print';
import './datatables';
import Select2 from 'select2';
import 'select2/dist/css/select2.css';
import '../css/select2-custom.css';  // Import custom CSS

window.$ = window.jQuery = $;
Select2();
