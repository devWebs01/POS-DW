# PRD — Level Biasa (Stakeholder Version)

## Fitur Utama

1. **Katalog Produk Digital** — Simpan data barang dagangan (nama, harga, stok) dengan kategori
2. **Kasir Digital** — Antar muka kasir cepat untuk proses transaksi sehari-hari
3. **Atur Stok Barang** — Pantau stok barang secara real-time
4. **Laporan Penjualan Harian** — Lihat total penjualan harian dengan mudah
5. **Keamanan Akses** — Admin dan kasir memiliki akses berbeda

## Use Cases Bisnis

- Kasir dapat memilih barang, melihat total, dan menyelesaikan transaksi dalam hitungan detik
- Pemilik toko dapat menambah barang baru ke katalog kapan saja
- Pemilik toko dapat melihat berapa total penjualan hari ini tanpa hitung manual
- Stok barang otomatis berkurang saat transaksi selesai

## Business Requirements

| ID | Requirement | Dampak |
|----|-------------|--------|
| BR-01 | Proses transaksi tidak boleh lebih dari 30 detik per pelanggan | Kepuasan pelanggan |
| BR-02 | Data penjualan harus akurat hingga senilai terkecil | Kepercayaan bisnis |
| BR-03 | Hanya kasir yang berwenang yang bisa memproses transaksi | Keamanan |
| BR-04 | Stok barang tidak boleh minus (wajib cek ketersediaan) | Akurasi inventaris |

## Success Metrics

- Waktu proses transaksi rata-rata < 10 detik
- Zero error perhitungan total/kembalian
- 100% data penjualan terekam dan bisa diaudit

## Constraints

- Cocok untuk toko dengan 1 lokasi saja
- Pembayaran tunai di tempat (cash only)
- Cetak struk dari printer thermal / kertas biasa
