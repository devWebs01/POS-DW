# BAB III
# METODOLOGI PENELITIAN

---

## 3.1 Metode Penelitian

> **⏳ Template — Konten perlu disesuaikan dengan metode penelitian yang digunakan.**

Penelitian ini menggunakan metode Research and Development (R&D) yang berfokus pada pengembangan produk perangkat lunak. Pendekatan yang digunakan adalah pendekatan rekayasa perangkat lunak dengan model pengembangan Agile menggunakan Rapid Application Development (RAD).

---

## 3.2 Kerangka Kerja Penelitian

> **⏳ Template — Konten perlu diisi dengan diagram kerangka kerja penelitian.**

Kerangka kerja penelitian ini terdiri dari beberapa tahapan sebagai berikut:

1. **Identifikasi Masalah** — Mengidentifikasi permasalahan dalam sistem POS konvensional dan kebutuhan akan sistem terintegrasi dengan Data Warehouse.
2. **Studi Literatur** — Mempelajari teori dan referensi yang relevan dengan penelitian.
3. **Pengumpulan Data** — Mengumpulkan data kebutuhan sistem melalui observasi, wawancara, dan studi dokumen.
4. **Analisis Kebutuhan** — Menganalisis kebutuhan fungsional dan non-fungsional sistem.
5. **Perancangan Sistem** — Merancang arsitektur sistem, diagram UML, struktur database, dan antarmuka.
6. **Implementasi** — Menerjemahkan perancangan ke dalam kode program.
7. **Pengujian** — Melakukan pengujian sistem untuk memvalidasi fungsionalitas.
8. **Dokumentasi** — Menyusun laporan penelitian dan dokumentasi sistem.

---

## 3.3 Metode Pengumpulan Data

> **⏳ Template — Konten perlu disesuaikan dengan metode yang digunakan.**

### 3.3.1 Studi Literatur

Studi literatur dilakukan dengan mempelajari buku, jurnal ilmiah, artikel, dan dokumentasi resmi yang berkaitan dengan:
- Sistem Point of Sale (POS)
- Data Warehouse
- Framework Laravel
- Livewire dan Flux UI
- Pengembangan aplikasi web

### 3.3.2 Observasi

Observasi dilakukan dengan mengamati proses bisnis dan alur kerja pada sistem POS yang sudah berjalan untuk mengidentifikasi kebutuhan dan kelemahan sistem yang ada.

### 3.3.3 Wawancara

Wawancara dilakukan dengan pihak-pihak yang terlibat dalam operasional sistem POS, meliputi pemilik toko, kasir, dan manajer.

---

## 3.4 Metode Pengembangan Sistem

> **⏳ Template — Konten perlu disesuaikan dengan metode pengembangan yang digunakan.**

Penelitian ini menggunakan metode Rapid Application Development (RAD) yang memiliki tahapan sebagai berikut:

```
┌──────────────────────┐
│ Requirements Planning│
│  (Perencanaan Kebutuhan)│
└──────────┬───────────┘
           ▼
┌──────────────────────┐
│   User Design        │
│  (Design Workshop)   │
└──────────┬───────────┘
           ▼
┌──────────────────────┐
│   Construction       │
│   (Iterasi Build)    │
└──────────┬───────────┘
           ▼
┌──────────────────────┐
│    Cutover           │
│  (Implementasi Akhir)│
└──────────────────────┘
```

### 3.4.1 Requirements Planning

Tahap ini meliputi identifikasi kebutuhan sistem melalui analisis terhadap proses bisnis dan pengumpulan data kebutuhan dari stakeholders.

### 3.4.2 User Design (Workshop Design)

Tahap perancangan sistem melibatkan pembuatan prototype dan iterasi desain berdasarkan feedback dari pengguna.

### 3.4.3 Construction (Iterasi Build)

Tahap implementasi kode program dilakukan secara iteratif dengan pengujian berkelanjutan.

### 3.4.4 Cutover (Implementasi Akhir)

Tahap implementasi akhir meliputi deployment sistem dan dokumentasi.

---

## 3.5 Metode Pengujian

> **⏳ Template — Konten perlu disesuaikan dengan metode pengujian yang digunakan.**

Pengujian sistem menggunakan metode **Black Box Testing** yang berfokus pada pengujian fungsionalitas sistem tanpa melihat struktur kode internal. Pengujian dilakukan dengan memberikan input tertentu dan membandingkan output yang dihasilkan dengan output yang diharapkan.

Skenario pengujian mencakup 4 modul utama:
1. **Modul Autentikasi** — Pengujian login, logout, 2FA, validasi input, dan brute force protection.
2. **Modul Kategori** — Pengujian CRUD kategori termasuk validasi duplikasi.
3. **Modul Produk** — Pengujian CRUD produk, upload gambar, filter, dan status aktif/nonaktif.
4. **Modul Transaksi** — Pengujian keranjang, pembayaran, stok, cetak struk, dan riwayat.

---

## 3.6 Jadwal Penelitian

> **⏳ Template — Konten perlu disesuaikan dengan jadwal penelitian yang sebenarnya.**

| No | Kegiatan | Bulan 1 | Bulan 2 | Bulan 3 | Bulan 4 |
|---|----------|:-------:|:-------:|:-------:|:-------:|
| 1 | Identifikasi Masalah | ███ | | | |
| 2 | Studi Literatur | ███ | ███ | | |
| 3 | Pengumpulan Data | | ███ | | |
| 4 | Analisis Kebutuhan | | ███ | ███ | |
| 5 | Perancangan Sistem | | | ███ | |
| 6 | Implementasi | | | ███ | ███ |
| 7 | Pengujian | | | | ███ |
| 8 | Dokumentasi | | | | ███ |

---

## Navigasi

| [← Bab II](./BAB-II-TINJAUAN-PUSTAKA.md) | [Daftar Isi](./README.md) | [Bab IV →](./BAB-IV-PERANCANGAN-DAN-IMPLEMENTASI.md) |
|:---:|:---:|:---:|

---

**© 2026 - Sistem POS DW Documentation**
