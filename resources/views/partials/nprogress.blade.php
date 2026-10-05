<!-- NProgress CSS & JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>

<style>
    /* Kustomisasi Warna NProgress menjadi Biru-Tosca Accent */
    #nprogress .bar {
        background: #70D6C5 !important;
        height: 4px !important; /* Sedikit lebih tebal agar terlihat jelas */
    }
    
    #nprogress .peg {
        box-shadow: 0 0 10px #70D6C5, 0 0 5px #70D6C5 !important;
    }
    
    #nprogress .spinner-icon {
        border-top-color: #70D6C5 !important;
        border-left-color: #70D6C5 !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Matikan spinner jika kurang disukai, atau biarkan default
        NProgress.configure({ showSpinner: false, minimum: 0.1, speed: 400 });
        
        // Ketika halaman selesai dimuat, hilangkan loading
        NProgress.done();

        // Tangkap event klik pada link
        document.querySelectorAll('a').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                // Abaikan jika link memiliki atribut target="_blank" (tab baru)
                // Abaikan jika href kosong atau menunjuk ke anchor id dalam halaman yang sama ('#')
                const href = this.getAttribute('href');
                const target = this.getAttribute('target');
                
                if (href && href !== '#' && !href.startsWith('javascript:') && target !== '_blank' && !e.ctrlKey && !e.metaKey) {
                    NProgress.start();
                }
            });
        });

        // Tangkap event saat form dikirim
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                NProgress.start();
            });
        });
    });

    // Handle back/forward history browser
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) {
            NProgress.done();
        }
    });
</script>
