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
    /* Kustomisasi khusus Toast agar lebih premium */
    div.swal2-toast {
        border-radius: 0.75rem !important;
        padding: 1rem !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid #e2e8f0 !important;
    }
    div.swal2-toast .swal2-title {
        font-size: 0.95rem !important;
        margin-left: 0.5rem !important;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const primaryColor = '#4D52B4'; // Biru-Tosca Utama

        // Helper Global SweetAlert2 (Toast) di pojok kanan atas
        window.Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            background: '#ffffff',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        // ==========================================
        // FLash Message Handling (Muncul Otomatis)
        // ==========================================

        @if(session('success'))
            window.Toast.fire({
                icon: 'success',
                title: {!! json_encode(session('success')) !!}
            });
        @endif

        @if(session('info') || session('status'))
            window.Toast.fire({
                icon: 'info',
                title: {!! json_encode(session('info') ?? session('status')) !!}
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan!',
                text: {!! json_encode(session('error')) !!},
                confirmButtonColor: primaryColor,
                confirmButtonText: 'Tutup'
            });
        @endif

        @if(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: {!! json_encode(session('warning')) !!},
                confirmButtonColor: primaryColor,
                confirmButtonText: 'Mengerti'
            });
        @endif

        // ==========================================
        // Helper Functions (Bisa dipanggil manual)
        // ==========================================
        
        window.notify = {
            success: function(message, title = 'Berhasil!') {
                return window.Toast.fire({
                    icon: 'success',
                    title: message
                });
            },
            error: function(message, title = 'Terjadi Kesalahan!') {
                return Swal.fire({
                    icon: 'error',
                    title: title,
                    text: message,
                    confirmButtonColor: primaryColor,
                    confirmButtonText: 'Tutup'
                });
            },
            warning: function(message, title = 'Peringatan!') {
                return Swal.fire({
                    icon: 'warning',
                    title: title,
                    text: message,
                    confirmButtonColor: primaryColor,
                    confirmButtonText: 'Mengerti'
                });
            },
            info: function(message, title = 'Informasi') {
                return window.Toast.fire({
                    icon: 'info',
                    title: message
                });
            },
            toast: function(icon, title) {
                return window.Toast.fire({
                    icon: icon,
                    title: title
                });
            }
        };
    });
</script>
