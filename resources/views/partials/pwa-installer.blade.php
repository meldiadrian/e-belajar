<!-- PWA Install Banner & Modal (Cross-Browser Support) -->
<div id="pwa-install-banner"
    class="fixed bottom-4 left-4 right-4 sm:left-auto sm:right-6 sm:max-w-md z-50 transform translate-y-32 opacity-0 transition-all duration-500 ease-out pointer-events-none">
    <div
        class="bg-slate-900/95 text-white border border-emerald-500/30 rounded-2xl shadow-2xl p-4 backdrop-blur-md flex items-center justify-between gap-3">
        <div class="flex items-center gap-3.5 min-w-0">
            <div
                class="w-12 h-12 rounded-xl bg-emerald-800/80 p-1.5 flex-shrink-0 flex items-center justify-center border border-emerald-500/30 shadow-inner">
                <img src="{{ asset('icons/icon-96x96.png') }}" alt="E-Belajar App Icon"
                    class="w-full h-full object-contain">
            </div>
            <div class="truncate">
                <div class="flex items-center gap-2">
                    <h5 class="text-sm font-bold text-white tracking-wide truncate">E-Belajar Bengkalis</h5>
                    <!-- <span
                        class="px-1.5 py-0.5 text-[10px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded">PWA</span> -->
                </div>
                <p class="text-xs text-slate-300 truncate mt-0.5">Pasang aplikasi untuk akses cepat & offline</p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-shrink-0">
            <button id="pwa-install-btn" type="button"
                class="px-3.5 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-semibold rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-1.5 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span>Pasang</span>
            </button>
            <button id="pwa-close-btn" type="button"
                class="text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800 transition-colors cursor-pointer"
                title="Tutup">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Modal Panduan Instalasi (Khusus iOS Safari / Browser yang tidak mendukung native prompt) -->
<div id="pwa-instruction-modal"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 transform scale-95 transition-transform duration-300"
        id="pwa-modal-content">
        <div class="text-center">
            <div
                class="w-16 h-16 rounded-2xl bg-emerald-50 border border-emerald-100 mx-auto flex items-center justify-center p-2 mb-4 shadow-sm">
                <img src="{{ asset('icons/icon-128x128.png') }}" alt="Logo" class="w-full h-full object-contain">
            </div>
            <h4 class="text-lg font-bold text-slate-800 mb-1">Pasang Aplikasi E-Belajar</h4>
            <p class="text-xs text-slate-500 mb-5" id="pwa-modal-subtitle">Ikuti langkah mudah berikut di perangkat
                Anda:</p>
        </div>

        <!-- Instruksi Khusus iOS Safari -->
        <div id="pwa-ios-instructions" class="space-y-3.5 mb-6 text-xs text-slate-600">
            <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span
                    class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">1</span>
                <div>
                    <span class="font-medium text-slate-800">Ketuk tombol Bagikan (Share)</span>
                    <p class="text-slate-500 mt-0.5">Ikon persegi dengan panah ke atas (<span
                            class="font-bold text-slate-700">⎋</span>) di bilah bawah browser Safari.</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span
                    class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">2</span>
                <div>
                    <span class="font-medium text-slate-800">Pilih 'Tambahkan ke Layar Utama'</span>
                    <p class="text-slate-500 mt-0.5">Gulir ke bawah pada menu opsi hingga menemukan pilihan <span
                            class="font-semibold text-slate-700">"Add to Home Screen"</span>.</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span
                    class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">3</span>
                <div>
                    <span class="font-medium text-slate-800">Tekan 'Tambah'</span>
                    <p class="text-slate-500 mt-0.5">Tekan <span class="font-semibold text-slate-700">"Add" /
                            "Tambah"</span> di pojok kanan atas. Ikon aplikasi akan muncul di layar utama.</p>
                </div>
            </div>
        </div>

        <!-- Instruksi Browser Lain / Desktop -->
        <div id="pwa-other-instructions" class="space-y-3.5 mb-6 text-xs text-slate-600 hidden">
            <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span
                    class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">1</span>
                <div>
                    <span class="font-medium text-slate-800">Buka Menu Browser</span>
                    <p class="text-slate-500 mt-0.5">Tekan ikon titik tiga (<span class="font-bold">⋮</span>) di pojok
                        kanan atas browser Anda.</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span
                    class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">2</span>
                <div>
                    <span class="font-medium text-slate-800">Pilih 'Pasang / Install Aplikasi'</span>
                    <p class="text-slate-500 mt-0.5">Atau klik ikon install di bilah alamat browser (URL bar).</p>
                </div>
            </div>
        </div>

        <button type="button" id="pwa-modal-close-btn"
            class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition-colors cursor-pointer">
            Mengerti, Tutup
        </button>
    </div>
</div>

<script>
    (function () {
        let deferredPrompt = null;
        const banner = document.getElementById('pwa-install-banner');
        const installBtn = document.getElementById('pwa-install-btn');
        const closeBtn = document.getElementById('pwa-close-btn');
        const modal = document.getElementById('pwa-instruction-modal');
        const modalContent = document.getElementById('pwa-modal-content');
        const modalCloseBtn = document.getElementById('pwa-modal-close-btn');
        const iosInstructions = document.getElementById('pwa-ios-instructions');
        const otherInstructions = document.getElementById('pwa-other-instructions');
        const modalSubtitle = document.getElementById('pwa-modal-subtitle');

        // Cek apakah aplikasi sudah dalam mode standalone (terpasang)
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches ||
            window.navigator.standalone === true ||
            document.referrer.includes('android-app://');

        if (isStandalone) {
            // Jika sudah dipasang dan dibuka via PWA, jangan tampilkan banner instalasi
            return;
        }

        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('{{ asset("sw.js") }}')
                    .then(function (reg) {
                        console.log('[E-Belajar PWA] Service Worker terdaftar dengan scope:', reg.scope);
                    })
                    .catch(function (err) {
                        console.warn('[E-Belajar PWA] Registrasi Service Worker gagal:', err);
                    });
            });
        }

        // Deteksi Platform
        const userAgent = window.navigator.userAgent.toLowerCase();
        const isIos = /iphone|ipad|ipod/.test(userAgent);
        const isDismissed = sessionStorage.getItem('pwa_banner_dismissed') === 'true';

        function showBanner() {
            if (isDismissed || isStandalone) return;
            banner.classList.remove('translate-y-32', 'opacity-0', 'pointer-events-none');
            banner.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
        }

        function hideBanner() {
            banner.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
            banner.classList.add('translate-y-32', 'opacity-0', 'pointer-events-none');
        }

        function openModal(mode) {
            if (mode === 'ios') {
                iosInstructions.classList.remove('hidden');
                otherInstructions.classList.add('hidden');
                modalSubtitle.textContent = 'Panduan pemasangan di Safari iOS (iPhone / iPad):';
            } else {
                iosInstructions.classList.add('hidden');
                otherInstructions.classList.remove('hidden');
                modalSubtitle.textContent = 'Panduan pemasangan di browser Anda:';
            }
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }

        function closeModal() {
            modal.classList.add('opacity-0');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Tangkap event beforeinstallprompt (Chrome, Chromium Edge, Android Browser, Opera, Brave)
        window.addEventListener('beforeinstallprompt', function (e) {
            e.preventDefault();
            deferredPrompt = e;
            // Tampilkan banner
            showBanner();
        });

        // Tangani browser iOS atau browser yang tidak memicu beforeinstallprompt secara otomatis
        setTimeout(function () {
            // Jika banner belum muncul (misal di iOS Safari atau desktop yang tidak ada prompt)
            if (!deferredPrompt && !isDismissed && !isStandalone) {
                showBanner();
            }
        }, 2000);

        // Event tombol Pasang
        installBtn.addEventListener('click', async function () {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                console.log('[E-Belajar PWA] User response:', outcome);
                deferredPrompt = null;
                hideBanner();
            } else if (isIos) {
                openModal('ios');
            } else {
                openModal('other');
            }
        });

        // Event tombol tutup
        closeBtn.addEventListener('click', function () {
            hideBanner();
            sessionStorage.setItem('pwa_banner_dismissed', 'true');
        });

        modalCloseBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        // Event app installed
        window.addEventListener('appinstalled', function () {
            console.log('[E-Belajar PWA] Berhasil dipasang sebagai aplikasi!');
            hideBanner();
            deferredPrompt = null;
        });
    })();
</script>