<!-- Scroll to Top Button -->
<button id="scrollToTopBtn" 
    class="fixed bottom-6 right-6 z-50 p-3.5 rounded-full bg-[#4D52B4] text-white shadow-lg shadow-[#4D52B4]/30 hover:bg-[#4E9CE8] hover:-translate-y-1 hover:shadow-xl hover:shadow-[#4E9CE8]/40 transition-all duration-300 opacity-0 translate-y-10 pointer-events-none flex items-center justify-center focus:outline-none"
    aria-label="Kembali ke atas">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"></path>
    </svg>
</button>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scrollToTopBtn = document.getElementById('scrollToTopBtn');
        const adminScrollContainer = document.querySelector('.overflow-y-auto');
        
        if (scrollToTopBtn) {
            const toggleButton = (scrollTop) => {
                if (scrollTop > 300) {
                    scrollToTopBtn.classList.remove('opacity-0', 'translate-y-10', 'pointer-events-none');
                    scrollToTopBtn.classList.add('opacity-100', 'translate-y-0');
                } else {
                    scrollToTopBtn.classList.remove('opacity-100', 'translate-y-0');
                    scrollToTopBtn.classList.add('opacity-0', 'translate-y-10', 'pointer-events-none');
                }
            };

            // Tampilkan tombol jika di-scroll lebih dari 300px (Public Page)
            window.addEventListener('scroll', () => toggleButton(window.scrollY));
            
            // Tampilkan tombol jika di-scroll lebih dari 300px (Admin Panel)
            if (adminScrollContainer) {
                adminScrollContainer.addEventListener('scroll', () => toggleButton(adminScrollContainer.scrollTop));
            }

            // Animasi menggulung mulus ke atas saat diklik
            scrollToTopBtn.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
                if (adminScrollContainer) {
                    adminScrollContainer.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        }
    });
</script>
