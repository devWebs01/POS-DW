# Tabel Kebutuhan Non-Fungsional

**Tabel RNF-01 s/d RNF-30: Daftar Kebutuhan Non-Fungsional Sistem POS DW**

| Kode | Aspek | Kebutuhan | Deskripsi |
|------|-------|-----------|-----------|
| RNF-01 | Performance | Response Time | Sistem harus merespons request user dalam waktu maksimal 2 detik |
| RNF-02 | Performance | Page Load Time | Halaman harus dimuat dalam waktu maksimal 3 detik |
| RNF-03 | Performance | Concurrent Users | Sistem harus dapat menangani minimal 50 user aktif bersamaan |
| RNF-04 | Performance | Transaction Processing | Sistem harus dapat memproses minimal 100 transaksi per jam |
| RNF-05 | Security | Password Encryption | Password user harus dienkripsi menggunakan algoritma bcrypt |
| RNF-06 | Security | HTTPS | Sistem harus menggunakan protokol HTTPS untuk semua komunikasi |
| RNF-07 | Security | SQL Injection Prevention | Sistem harus terproteksi dari SQL Injection menggunakan prepared statements |
| RNF-08 | Security | XSS Prevention | Sistem harus terproteksi dari serangan Cross-Site Scripting |
| RNF-09 | Security | CSRF Protection | Sistem harus terproteksi dari serangan CSRF |
| RNF-10 | Security | Session Security | Session harus memiliki timeout otomatis setelah 120 menit |
| RNF-11 | Security | Role-Based Access Control | Sistem harus menerapkan kontrol akses berbasis role (RBAC) |
| RNF-12 | Usability | User Interface | Interface harus intuitif dan mudah digunakan tanpa training khusus |
| RNF-13 | Usability | Responsive Design | Interface harus responsive di desktop, tablet, dan mobile |
| RNF-14 | Usability | Error Messages | Sistem harus menampilkan pesan error yang jelas dan informatif |
| RNF-15 | Usability | Loading Indicators | Sistem harus menampilkan loading indicator saat memproses request |
| RNF-16 | Compatibility | Browser Support | Sistem harus kompatibel dengan Chrome, Firefox, Edge, Safari |
| RNF-17 | Compatibility | Screen Resolution | Sistem harus dapat ditampilkan pada resolusi minimal 1366x768 |
| RNF-18 | Reliability | Uptime | Sistem harus memiliki uptime minimal 99% |
| RNF-19 | Reliability | Data Backup | Sistem harus memiliki mekanisme backup database otomatis setiap hari |
| RNF-20 | Reliability | Error Recovery | Sistem harus dapat recovery dari error tanpa kehilangan data transaksi |
| RNF-21 | Maintainability | Code Quality | Source code harus mengikuti PSR-12 coding standard |
| RNF-22 | Maintainability | Documentation | Sistem harus memiliki dokumentasi kode dan API yang lengkap |
| RNF-23 | Maintainability | Modular Architecture | Sistem harus menggunakan arsitektur modular |
| RNF-24 | Scalability | Database Scalability | Database harus dapat menampung minimal 1 juta record transaksi |
| RNF-25 | Scalability | Horizontal Scaling | Sistem harus mendukung horizontal scaling |
| RNF-26 | Localization | Timezone | Sistem harus mendukung pengaturan timezone (default: Asia/Jakarta) |
| RNF-27 | Localization | Currency Format | Sistem harus mendukung format mata uang Rupiah (Rp) |
| RNF-28 | Localization | Date Format | Sistem harus menggunakan format tanggal Indonesia (DD/MM/YYYY) |
| RNF-29 | Availability | 24/7 Access | Sistem harus dapat diakses 24 jam sehari, 7 hari seminggu |
| RNF-30 | Compliance | Data Privacy | Sistem harus mematuhi regulasi perlindungan data pribadi |

