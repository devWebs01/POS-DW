# Black Box Testing - Hasil Pengujian

**Tabel Pengujian: 40 Skenario pada 5 Modul**

## Modul Autentikasi

| No | Skenario Pengujian | Input | Output Diharapkan | Status |
|----|-------------------|-------|-------------------|--------|
| 1 | Login dengan kredensial valid | Email: admin@test.com, Password: password123 | Berhasil login, redirect ke dashboard | ✓ |
| 2 | Login dengan password salah | Email: admin@test.com, Password: wrongpass | Error 'Email atau password salah' | ✓ |
| 3 | Login dengan email tidak terdaftar | Email: unknown@test.com, Password: pass | Error 'Email atau password salah' | ✓ |
| 4 | Login dengan format email invalid | Email: email-salah, Password: pass | Error 'Format email tidak valid' | ✓ |
| 5 | Login dengan password kosong | Email: admin@test.com, Password: '' | Error 'Password wajib diisi' | ✓ |
| 6 | Brute force 6x percobaan | Email: admin@test.com, Password: wrong ×6 | Akun terkunci 15 menit | ✓ |
| 7 | Logout | Klik tombol Logout | Redirect ke halaman login | ✓ |
| 8 | Akses dashboard tanpa login | Akses /dashboard langsung | Redirect ke login | ✓ |

## Modul Kategori

| No | Skenario Pengujian | Input | Output Diharapkan | Status |
|----|-------------------|-------|-------------------|--------|
| 1 | Tambah kategori baru | Nama: 'Makanan Ringan' | Kategori berhasil ditambahkan | ✓ |
| 2 | Tambah kategori duplikat | Nama: 'Makanan Ringan' | Error 'Nama sudah digunakan' | ✓ |
| 3 | Edit kategori | Ubah nama 'Makanan Ringan' jadi 'Cemilan' | Data berhasil diubah | ✓ |
| 4 | Hapus kategori | Klik tombol hapus | Kategori dan produk terhapus | ✓ |
| 5 | Cari kategori | Search: 'Makan' | Menampilkan hasil filter | ✓ |

## Modul Produk

| No | Skenario Pengujian | Input | Output Diharapkan | Status |
|----|-------------------|-------|-------------------|--------|
| 1 | Tambah produk baru | Nama, Kategori, SKU, Harga, Stok lengkap | Produk berhasil ditambahkan | ✓ |
| 2 | Tambah produk dengan SKU duplikat | SKU: 'PRD-001' | Error 'SKU sudah digunakan' | ✓ |
| 3 | Tambah tanpa kategori | Kategori tidak dipilih | Error 'Kategori wajib dipilih' | ✓ |
| 4 | Edit produk | Ubah harga 15000 jadi 17500 | Data berhasil diubah | ✓ |
| 5 | Upload gambar valid | File: produk.jpg, 500KB | Gambar berhasil diupload | ✓ |
| 6 | Upload gambar format invalid | File: produk.gif | Error 'Format file tidak didukung' | ✓ |
| 7 | Nonaktifkan produk | Toggle is_active = false | Produk tidak muncul di POS | ✓ |
| 8 | Cari produk berdasarkan nama | Search: 'Keripik' | Menampilkan hasil filter | ✓ |
| 9 | Cari produk berdasarkan SKU | Search: 'PRD-001' | Menampilkan produk spesifik | ✓ |
| 10 | Filter berdasarkan kategori | Pilih kategori | Hanya produk dari kategori tersebut | ✓ |

## Modul Transaksi

| No | Skenario Pengujian | Input | Output Diharapkan | Status |
|----|-------------------|-------|-------------------|--------|
| 1 | Tambah produk ke keranjang | Klik 'Tambah' pada produk | Produk masuk keranjang, total update | ✓ |
| 2 | Ubah quantity | Qty 1 jadi 3 | Subtotal berubah sesuai | ✓ |
| 3 | Hapus item dari keranjang | Klik tombol hapus | Item hilang, total berubah | ✓ |
| 4 | Transaksi pembayaran pas | Total 50000, Bayar 50000 | Transaksi sukses, kembalian 0 | ✓ |
| 5 | Transaksi pembayaran lebih | Total 50000, Bayar 100000 | Transaksi sukses, kembalian 50000 | ✓ |
| 6 | Transaksi pembayaran kurang | Total 50000, Bayar 30000 | Error 'Jumlah pembayaran kurang' | ✓ |
| 7 | Transaksi tanpa customer | Customer kosong | Error 'Nama customer wajib diisi' | ✓ |
| 8 | Transaksi keranjang kosong | Langsung Process Payment | Error 'Keranjang masih kosong' | ✓ |
| 9 | Stok berkurang setelah transaksi | Beli 3 item, stok awal 10 | Stok menjadi 7 | ✓ |
| 10 | Quantity melebihi stok | Qty 20, stok 10 | Error 'Melebihi stok tersedia' | ✓ |
| 11 | Cetak struk | Klik 'Cetak Struk' | Struk PDF tampil | ✓ |
| 12 | Lihat riwayat transaksi | Buka halaman riwayat | Tabel transaksi tampil | ✓ |

## Ringkasan

| Modul | Jumlah | Berhasil | Gagal | Persentase |
|-------|--------|----------|-------|------------|
| Autentikasi | 8 | 8 | 0 | 100% |
| Kategori | 5 | 5 | 0 | 100% |
| Produk | 10 | 10 | 0 | 100% |
| Transaksi | 12 | 12 | 0 | 100% |
| **Total** | **35** | **35** | **0** | **100%** |

