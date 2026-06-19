# BAB IV
# ANALISA DAN PERANCANGAN SISTEM

---

## Informasi Dokumen

**Judul Sistem:** Sistem Point of Sale Data Warehouse (POS DW)  
**Framework:** Laravel 13.9.0  
**Teknologi:** PHP 8.3, Livewire 4.3, MySQL 8.0, Tailwind CSS 4  
**Tanggal Dibuat:** 15 Juni 2026  
**Versi Dokumentasi:** 1.1  

---

## Daftar Isi

### [4.0 Analisis Sistem](./4.0-analisis-sistem.md)
- 4.0.1 Tentang Sistem POS DW
- 4.0.2 Analisa Sistem Yang Sedang Berjalan
- 4.0.3 Solusi Permasalahan Sistem

### [4.1 Analisa Kebutuhan Sistem](./4.1-analisis-kebutuhan-sistem.md)
- 4.1.1 Kebutuhan Fungsional
- 4.1.2 Kebutuhan Non-Fungsional
- 4.1.3 Kebutuhan Hardware dan Software

### [4.2 Analisis Proses Sistem](./4.2-perancangan-sistem.md)
- 4.2.1 Use Case Diagram
- 4.2.2 Deskripsi Use Case
- 4.2.3 Activity Diagram
- 4.2.4 Sequence Diagram
- 4.2.5 Class Diagram
- 4.2.6 Analisis Input dan Output

### [4.3 Perancangan Basis Data](./4.3-perancangan-basis-data.md)
- 4.3.1 Entity Relationship Diagram (ERD)
- 4.3.2 Struktur Tabel Database

### [4.4 Perancangan Antarmuka](./4.4-perancangan-antarmuka.md)
- 4.4.1 Rancangan Struktur Program
- 4.4.2 Rancangan Antarmuka Login
- 4.4.3 Rancangan Antarmuka Dashboard
- 4.4.4 Rancangan Antarmuka Manajemen Produk
- 4.4.5 Rancangan Antarmuka Transaksi POS
- 4.4.6 Rancangan Antarmuka Laporan Penjualan
- 4.4.7 Rancangan Antarmuka User Management
- 4.4.8 Rancangan Antarmuka Manajemen Stok

### [4.5 Implementasi Sistem](./4.5-implementasi-sistem.md)
- 4.5.1 Implementasi Database
- 4.5.2 Implementasi Program
- 4.5.3 Implementasi Tampilan

### [4.6 Pengujian Sistem](./4.6-pengujian-sistem.md)
- 4.6.1 Black Box Testing
- 4.6.2 User Acceptance Test (UAT)
- 4.6.3 Hasil Pengujian
- 4.6.4 Pembahasan Hasil

## Pengantar

Bab ini membahas analisis, perancangan, dan implementasi Sistem Point of Sale Data Warehouse (POS DW) yang dibangun menggunakan framework Laravel 13. Bab IV terdiri dari tujuh sub-bab utama, yaitu: Analisis Sistem yang menjelaskan profil objek penelitian dan permasalahan yang dihadapi; Analisa Kebutuhan Sistem yang mendefinisikan kebutuhan fungsional dan non-fungsional; Analisis Proses Sistem yang memodelkan sistem menggunakan diagram UML; Perancangan Basis Data yang mendefinisikan struktur data menggunakan ERD dan skema tabel; Perancangan Antarmuka yang mencakup rancangan struktur program dan desain antarmuka pengguna; Implementasi Sistem yang menjelaskan penerjemahan perancangan ke dalam kode program; serta Pengujian Sistem yang memverifikasi bahwa sistem telah berfungsi sesuai dengan kebutuhan yang telah didefinisikan.

### Ruang Lingkup Sistem

Sistem POS DW mencakup fitur-fitur utama sebagai berikut:

1. **Autentikasi dan Otorisasi**
   - Login dengan email dan password
   - Two-Factor Authentication (2FA)
   - Role-based access control (Admin, Kasir, Manager)
   - Permission management

2. **Manajemen Master Data**
   - Kategori produk
   - Data produk (CRUD, upload gambar, manajemen stok)
   - Manajemen user

3. **Transaksi Penjualan**
   - Point of Sale (POS) interface
   - Pencatatan transaksi real-time
   - Cetak struk atau invoice
   - Multi payment method (Cash, Debit, Credit, E-Wallet)

4. **Laporan dan Analitik**
   - Laporan penjualan per periode
   - Laporan stok produk
   - Produk terlaris
   - Export ke PDF dan Excel

5. **Pengaturan Sistem**
   - Konfigurasi toko
   - Profile user
   - Security settings

### Metodologi Pengembangan

Sistem ini dikembangkan menggunakan metodologi Agile dengan pendekatan Rapid Application Development (RAD) yang menekankan pada prototyping cepat, iterasi pengembangan, feedback kontinyu, dan deployment bertahap.

### Arsitektur Sistem

Sistem POS DW mengadopsi arsitektur MVC (Model-View-Controller) dengan enhancement dari Livewire untuk reactive components:

```
┌─────────────┐
│   Browser   │
└──────┬──────┘
       │ HTTP Request
       ▼
┌─────────────────────────────┐
│      Laravel Router         │
│    (web.php + Folio)        │
└──────────┬──────────────────┘
           │
           ▼
┌─────────────────────────────┐
│   Livewire Components       │
│   (Reactive UI Logic)       │
└──────────┬──────────────────┘
           │
           ▼
┌─────────────────────────────┐
│    Eloquent Models          │
│  (Business Logic Layer)     │
└──────────┬──────────────────┘
           │
           ▼
┌─────────────────────────────┐
│      MySQL Database         │
└─────────────────────────────┘
```

### Stack Teknologi

| Layer | Teknologi |
|-------|-----------|
| **Frontend** | Livewire 4, Livewire Flux UI, Tailwind CSS 4, Alpine.js |
| **Backend** | Laravel 13, PHP 8.3 |
| **Database** | MySQL 8.0 |
| **Authentication** | Laravel Fortify + 2FA |
| **Authorization** | Spatie Laravel Permission |
| **Routing** | Laravel Folio (Page-based routing) |
| **Asset Build** | Vite 5 |
| **Testing** | PHPUnit 12, Pest |

*Sumber: Dokumentasi Teknis (2026)*

---

## Navigasi Dokumen

| [← Daftar Isi](./README.md) | [Mulai: 4.0 →](./4.0-analisis-sistem.md) | [Bab V →](./BAB-V-KESIMPULAN-DAN-SARAN.md) |
|:---:|:---:|:---:|

---

## Catatan Penggunaan

1. **Diagram UML:** Semua diagram tersedia dalam dua format:
   - **PNG** (siap pakai) di `docs/bab-iv/diagrams/*.png`
   - **PlantUML** (dapat diedit) di `docs/bab-iv/diagrams/*.puml`

2. **Tabel Data:** Tabel-tabel pendukung disimpan di `docs/bab-iv/tables/`:
   - `kebutuhan-fungsional.md` - 35 kebutuhan fungsional
   - `kebutuhan-non-fungsional.md` - 30 kebutuhan non-fungsional
   - `hardware-software.md` - Spesifikasi hardware dan software
   - `black-box-testing.md` - 35 skenario pengujian

3. **Screenshot:** Tersedia wireframe SVG di `docs/bab-iv/images/`. Screenshot aktual sebaiknya diambil dari aplikasi yang running untuk menggantikan file SVG.

4. **Format Akademik:** Dokumentasi ini dapat di-copy ke Microsoft Word atau LaTeX dengan penyesuaian format sesuai template kampus.

---

**© 2026 - Sistem POS DW Documentation**
