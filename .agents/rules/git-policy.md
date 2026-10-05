# Git Policy - Manual Control Only

- **Dilarang keras melakukan `git push` atau `git pull` secara otomatis (autopilot).**
- Semua operasi yang menyentuh remote repository GitHub (`git push`, `git pull`, `git fetch`) hanya boleh dieksekusi jika pengguna meminta secara eksplisit.
- Semua pengerjaan kode, debugging, dan testing dilakukan murni pada branch lokal tanpa melakukan sinkronisasi otomatis ke remote.
