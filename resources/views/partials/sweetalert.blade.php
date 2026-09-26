<!-- SweetAlert2 CDN & Setup -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    div.swal2-popup {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        border-radius: 1rem !important;
    }
    .swal2-title {
        font-weight: 700 !important;
        font-size: 1.25rem !important;
        color: #0f172a !important;
    }
    .swal2-html-container {
        font-size: 0.925rem !important;
        color: #475569 !important;
        line-height: 1.5 !important;
    }
    .swal2-confirm, .swal2-cancel {
        border-radius: 0.75rem !important;
        font-weight: 600 !important;
        font-size: 0.875rem !important;
        padding: 0.625rem 1.5rem !important;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bengkalisGreen = '#047857';

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: {!! json_encode(session('success')) !!},
                confirmButtonColor: bengkalisGreen,
                confirmButtonText: 'OK',
                timer: 3500,
                timerProgressBar: true
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan!',
                text: {!! json_encode(session('error')) !!},
                confirmButtonColor: bengkalisGreen,
                confirmButtonText: 'Tutup'
            });
        @endif

        @if(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: {!! json_encode(session('warning')) !!},
                confirmButtonColor: bengkalisGreen,
                confirmButtonText: 'Mengerti'
            });
        @endif

        @if(session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: {!! json_encode(session('info')) !!},
                confirmButtonColor: bengkalisGreen,
                confirmButtonText: 'OK',
                timer: 4000,
                timerProgressBar: true
            });
        @endif

        @if(session('status'))
            Swal.fire({
                icon: 'info',
                title: 'Status',
                text: {!! json_encode(session('status')) !!},
                confirmButtonColor: bengkalisGreen,
                confirmButtonText: 'OK',
                timer: 3500,
                timerProgressBar: true
            });
        @endif
    });

    // Helper Global SweetAlert2 untuk dipanggil dari script halaman mana saja
    window.Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    window.notify = {
        success: function(message, title = 'Berhasil!') {
            return Swal.fire({
                icon: 'success',
                title: title,
                text: message,
                confirmButtonColor: '#047857',
                confirmButtonText: 'OK',
                timer: 3500,
                timerProgressBar: true
            });
        },
        error: function(message, title = 'Terjadi Kesalahan!') {
            return Swal.fire({
                icon: 'error',
                title: title,
                text: message,
                confirmButtonColor: '#047857',
                confirmButtonText: 'Tutup'
            });
        },
        warning: function(message, title = 'Peringatan!') {
            return Swal.fire({
                icon: 'warning',
                title: title,
                text: message,
                confirmButtonColor: '#047857',
                confirmButtonText: 'Mengerti'
            });
        },
        info: function(message, title = 'Informasi') {
            return Swal.fire({
                icon: 'info',
                title: title,
                text: message,
                confirmButtonColor: '#047857',
                confirmButtonText: 'OK',
                timer: 4000,
                timerProgressBar: true
            });
        },
        toast: function(icon, title) {
            return window.Toast.fire({
                icon: icon,
                title: title
            });
        }
    };
</script>
