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

// Global Form Submit Handler (Loading State)
document.addEventListener('submit', function(e) {
    const target = e.target as HTMLFormElement;
    
    // Ignora form-delete (que tem seu próprio handler do SweetAlert)
    if (target.classList.contains('form-delete')) {
        return;
    }

    // Procura o botão de submit e adiciona estado de loading
    const submitBtn = target.querySelector('button[type="submit"]') as HTMLButtonElement;
    if (submitBtn) {
        submitBtn.disabled = true;
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processando...';
        submitBtn.setAttribute('data-original-text', originalText);
    }
});

// Global Delete Form Handler (SweetAlert Confirmation)
document.addEventListener('submit', function(e) {
    const target = e.target as HTMLFormElement;
    
    if (target.classList.contains('form-delete')) {
        e.preventDefault(); // Impede o envio imediato
        
        Swal.fire({
            title: 'Tem certeza?',
            text: "Esta ação não poderá ser revertida!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const submitBtn = target.querySelector('button[type="submit"]') as HTMLButtonElement;
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Excluindo...';
                }
                target.submit();
            }
        });
    }
});

// Theme Manager
const getStoredTheme = () => localStorage.getItem('theme');
const setStoredTheme = (theme: string) => localStorage.setItem('theme', theme);

const getPreferredTheme = () => {
    const storedTheme = getStoredTheme();
    if (storedTheme) {
        return storedTheme;
    }
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
};

const setTheme = (theme: string) => {
    if (theme === 'auto') {
        document.documentElement.setAttribute('data-bs-theme', (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'));
    } else {
        document.documentElement.setAttribute('data-bs-theme', theme);
    }
};

setTheme(getPreferredTheme());

const showActiveTheme = (theme: string) => {
    const themeSwitcherText = document.querySelector('#bd-theme-text');
    const activeThemeIcon = document.querySelector('.theme-icon-active');
    const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`);
    
    document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
        element.classList.remove('active');
        element.setAttribute('aria-pressed', 'false');
    });

    if (btnToActive) {
        btnToActive.classList.add('active');
        btnToActive.setAttribute('aria-pressed', 'true');
        const iconOfActiveBtn = btnToActive.querySelector('i')?.className;
        if (activeThemeIcon && iconOfActiveBtn) {
            activeThemeIcon.className = iconOfActiveBtn + ' theme-icon-active';
        }
    }
};

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    const storedTheme = getStoredTheme();
    if (storedTheme !== 'light' && storedTheme !== 'dark') {
        setTheme(getPreferredTheme());
    }
});

document.addEventListener('DOMContentLoaded', () => {
    showActiveTheme(getStoredTheme() || 'auto');

    document.querySelectorAll('[data-bs-theme-value]').forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            const theme = toggle.getAttribute('data-bs-theme-value');
            if (theme) {
                setStoredTheme(theme);
                setTheme(theme);
                showActiveTheme(theme);
            }
        });
    });
});
