import './bootstrap';
import * as bootstrap from 'bootstrap';
import Swal from 'sweetalert2';

import $ from 'jquery';
import DataTable from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.min.css';

window.bootstrap = bootstrap;
window.Swal = Swal;
window.$ = window.jQuery = $;
window.DataTable = DataTable;

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

window.Toast = Toast;
