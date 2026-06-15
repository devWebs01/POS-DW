# BAB IV
# PERANCANGAN DAN IMPLEMENTASI SISTEM

---

## Informasi Dokumen

**Judul Sistem:** Sistem Point of Sale Data Warehouse (POS DW)  
**Framework:** Laravel 13.9.0  
**Teknologi:** PHP 8.3, Livewire 4.3, MySQL 8.0, Tailwind CSS 4  
**Tanggal Dibuat:** 15 Juni 2026  
**Versi Dokumentasi:** 1.0  

---

## Daftar Isi

### [4.1 Analisis Kebutuhan Sistem](./4.1-analisis-kebutuhan-sistem.md)
- 4.1.1 Kebutuhan Fungsional
- 4.1.2 Kebutuhan Non-Fungsional
- 4.1.3 Kebutuhan Hardware dan Software

### [4.2 Perancangan Sistem](./4.2-perancangan-sistem.md)
- 4.2.1 Flowchart Sistem
- 4.2.2 Use Case Diagram
- 4.2.3 Activity Diagram
- 4.2.4 Sequence Diagram
- 4.2.5 Class Diagram

### [4.3 Perancangan Basis Data](./4.3-perancangan-basis-data.md)
- 4.3.1 Entity Relationship Diagram (ERD)
- 4.3.2 Struktur Tabel

### [4.4 Perancangan Antarmuka](./4.4-perancangan-antarmuka.md)
- 4.4.1 Halaman Login & Autentikasi
- 4.4.2 Dashboard
- 4.4.3 Manajemen Kategori & Produk
- 4.4.4 Transaksi POS
- 4.4.5 Laporan & Reports
- 4.4.6 Manajemen User, Role & Permission
- 4.4.7 Settings & Security (2FA)

### [4.5 Implementasi Sistem](./4.5-implementasi-sistem.md)
- 4.5.1 Implementasi Database
- 4.5.2 Implementasi Program
- 4.5.3 Implementasi Tampilan

### [4.6 Pengujian Sistem](./4.6-pengujian-sistem.md)
- 4.6.1 Black Box Testing
- 4.6.2 User Acceptance Test (UAT)
- 4.6.3 Hasil Pengujian
- 4.6.4 Pembahasan Hasil

---

## Pengantar

Bab ini membahas perancangan dan implementasi Sistem Point of Sale Data Warehouse (POS DW) yang dibangun menggunakan framework Laravel 13. Sistem ini dirancang untuk memenuhi kebutuhan bisnis retail dalam mengelola transaksi penjualan, manajemen produk, inventori, dan laporan penjualan.

### Ruang Lingkup Sistem

Sistem POS DW mencakup fitur-fitur utama:

1. **Autentikasi & Otorisasi**
   - Login dengan email & password
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
   - Cetak struk/invoice
   - Multi payment method (Cash, Debit, Credit, E-Wallet)

4. **Laporan & Analitik**
   - Laporan penjualan per periode
   - Laporan stok produk
   - Produk terlaris
   - Export ke PDF dan Excel

5. **Pengaturan Sistem**
   - Konfigurasi toko
   - Profile user
   - Security settings

### Metodologi Pengembangan

Sistem ini dikembangkan menggunakan metodologi **Agile** dengan pendekatan **Rapid Application Development (RAD)** yang menekankan pada:
- Prototyping cepat
- Iterasi pengembangan
- Feedback kontinyu
- Deployment bertahap

### Arsitektur Sistem

Sistem POS DW mengadopsi arsitektur **MVC (Model-View-Controller)** dengan enhancement dari Livewire untuk reactive components:

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

---

## Navigasi Dokumen

📖 **Mulai dari sini:** [4.1 Analisis Kebutuhan Sistem →](./4.1-analisis-kebutuhan-sistem.md)

---

## Catatan Penggunaan

1. **Diagram UML:** Semua diagram sudah tersedia dalam dua format:
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
