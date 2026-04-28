import { SweetAlertOptions } from "sweetalert2";

declare global {
    interface Window {
        bootstrap: any;
        Swal: any;
        Toast: any;
    }
}
