# PRD — Level Biasa (Developer Version)

## Fitur Utama

1. **Manajemen Produk** — CRUD produk (nama, harga, stok, SKU)
2. **Transaksi POS** — Kasir cepat dengan pilih produk, hitung total, terima pembayaran
3. **Manajemen Kategori** — Kategorisasi produk sederhana
4. **Laporan Harian** — Rekap penjualan per hari
5. **Manajemen Pengguna** — Role: admin dan kasir (dengan Fortify)

## User Stories

- Sebagai kasir, saya ingin memilih produk dan memproses pembayaran dengan cepat
- Sebagai admin, saya ingin menambah/mengubah produk dan kategorinya
- Sebagai admin, saya ingin melihat laporan penjualan harian
- Sebagai pengguna, saya ingin login dengan aman

## Functional Requirements

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-01 | Sistem dapat menampilkan daftar produk dengan pencarian | HIGH |
| FR-02 | Sistem dapat memproses transaksi dengan minimal 1 item | HIGH |
| FR-03 | Sistem menghitung total dan kembalian secara otomatis | HIGH |
| FR-04 | Sistem menyimpan riwayat transaksi | HIGH |
| FR-05 | Sistem mendukung role admin dan kasir | HIGH |
| FR-06 | Sistem dapat menampilkan laporan penjualan harian | MEDIUM |
| FR-07 | Produk dapat difilter berdasarkan kategori | MEDIUM |
| FR-08 | Stok otomatis berkurang saat transaksi | HIGH |

## Non-Functional Requirements

| ID | Requirement | Target |
|----|-------------|--------|
| NFR-01 | Response time transaksi | < 2 detik |
| NFR-02 | Bahasa antarmuka | Bahasa Indonesia |
| NFR-03 | Arsitektur | Single-tenant |
| NFR-04 | Cetak struk | Thermal printer / browser print |

## Constraints

- Hanya 1 toko per instance
- Tidak ada manajemen stok multi-gudang
- Tidak ada pelanggan tersimpan
- Pembayaran tunai saja

## Dependency Antar Fitur

```
Produk & Kategori → Transaksi POS → Laporan Harian
       ↑
  Manajemen User (auth)
```

## Rekomendasi Teknologi

| Library | Fungsi |
|---------|--------|
| Laravel 13 + Livewire 4 | Framework utama & frontend reaktif |
| Flux 2 | UI component library |
| Laravel Fortify | Authentication (existing) |
| barryvdh/laravel-dompdf | Cetak struk PDF |
