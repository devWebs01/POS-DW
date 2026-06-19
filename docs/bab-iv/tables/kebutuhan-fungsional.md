# Tabel Kebutuhan Fungsional

**Tabel RF-01 s/d RF-35: Daftar Kebutuhan Fungsional Sistem POS DW**

| Kode | Kebutuhan Fungsional | Deskripsi | Aktor |
|------|---------------------|-----------|-------|
| RF-01 | Login dan Autentikasi | Sistem harus dapat melakukan autentikasi user menggunakan email dan password | Admin, Pemilik |
| RF-02 | Two-Factor Authentication (2FA) | Sistem harus mendukung autentikasi dua faktor menggunakan aplikasi authenticator | Admin, Pemilik |
| RF-03 | Manajemen Kategori Produk | Sistem harus dapat mengelola data kategori produk (Create, Read, Update, Delete) | Admin |
| RF-04 | Manajemen Produk | Sistem harus dapat mengelola data produk meliputi nama, SKU, harga, stok, gambar, dan deskripsi | Admin |
| RF-05 | Upload Gambar Produk | Sistem harus dapat menerima upload gambar produk dengan format JPG, PNG, WEBP | Admin |
| RF-06 | Manajemen Stok Produk | Sistem harus dapat mengelola stok produk dengan opsi stok terbatas atau unlimited | Admin |
| RF-07 | Pencarian dan Filter Produk | Sistem harus dapat melakukan pencarian produk berdasarkan nama, SKU, atau kategori | Admin |
| RF-08 | Transaksi Penjualan (POS) | Sistem harus dapat memproses transaksi penjualan dengan interface point of sale | Admin |
| RF-09 | Keranjang Belanja | Sistem harus dapat menambah, mengurangi, dan menghapus item di keranjang belanja | Admin |
| RF-10 | Kalkulasi Otomatis | Sistem harus dapat menghitung total transaksi, kembalian, dan subtotal secara otomatis | Admin |
| RF-11 | Multi Payment Method | Sistem harus mendukung metode pembayaran: Cash, Debit Card, Credit Card, E-Wallet | Admin |
| RF-12 | Cetak Struk/Invoice | Sistem harus dapat mencetak atau mendownload struk transaksi dalam format PDF | Admin |
| RF-13 | Validasi Stok Real-time | Sistem harus memvalidasi ketersediaan stok produk sebelum transaksi diproses | Admin |
| RF-14 | Update Stok Otomatis | Sistem harus otomatis mengurangi stok produk setelah transaksi berhasil | Sistem |
| RF-15 | Riwayat Transaksi | Sistem harus dapat menampilkan riwayat transaksi dengan detail lengkap | Admin, Pemilik |
| RF-16 | Detail Transaksi | Sistem harus dapat menampilkan detail transaksi termasuk item yang dibeli | Admin, Pemilik |
| RF-17 | Laporan Penjualan | Sistem harus dapat menghasilkan laporan penjualan berdasarkan periode waktu tertentu | Admin, Pemilik |
| RF-18 | Laporan Stok Produk | Sistem harus dapat menghasilkan laporan stok produk dengan status stok menipis | Admin, Pemilik |
| RF-19 | Laporan Produk Terlaris | Sistem harus dapat menghasilkan laporan produk terlaris berdasarkan jumlah penjualan | Admin, Pemilik |
| RF-20 | Filter Laporan | Sistem harus dapat memfilter laporan berdasarkan tanggal, kategori, atau produk | Admin, Pemilik |
| RF-21 | Export Laporan PDF | Sistem harus dapat mengekspor laporan ke format PDF | Admin, Pemilik |
| RF-22 | Export Laporan Excel | Sistem harus dapat mengekspor laporan ke format Excel (XLSX) | Admin, Pemilik |
| RF-23 | Manajemen User | Sistem harus dapat mengelola data user (Create, Read, Update, Delete) | Admin |
| RF-24 | Manajemen Role | Sistem harus dapat mengelola role pengguna (Admin, Pemilik) | Admin |
| RF-25 | Manajemen Permission | Sistem harus dapat mengelola hak akses per role | Admin |
| RF-26 | Assign Role ke User | Sistem harus dapat mengassign role kepada user | Admin |
| RF-27 | Settings Toko | Sistem harus dapat mengatur konfigurasi toko (nama, alamat, logo, telepon) | Admin |
| RF-28 | Settings Profil User | Sistem harus dapat mengubah profil user (nama, email) | Admin, Pemilik |
| RF-29 | Ubah Password | Sistem harus dapat mengubah password user | Admin, Pemilik |
| RF-30 | Logout | Sistem harus dapat melakukan logout dan menghapus session | Admin, Pemilik |
| RF-31 | Dashboard Statistik | Sistem harus menampilkan dashboard dengan statistik penjualan, transaksi, dan stok | Admin, Pemilik |
| RF-32 | Notifikasi Stok Menipis | Sistem harus memberikan notifikasi jika stok produk menipis (kurang dari threshold) | Admin |
| RF-33 | Validasi Input | Sistem harus melakukan validasi input pada setiap form entry | Admin, Pemilik |
| RF-34 | Password Reset | Sistem harus menyediakan fitur reset password melalui email | Admin, Pemilik |
| RF-35 | Session Management | Sistem harus dapat mengelola session user dengan timeout otomatis | Admin, Pemilik |
