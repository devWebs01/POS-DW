# Implementation Plan: Laravel POS System — PRD & ERD

## Requirements Restatement

Membangun dokumentasi PRD (Product Requirements Document) dan ERD (Entity Relationship Diagram) untuk sistem POS berbasis Laravel dengan tiga level kompleksitas (Biasa, Standar, Kompleks). Setiap level memiliki dua versi dokumentasi: **untuk Developer/Engineer** (detail teknis, implementasi-ready) dan **untuk Stakeholder Bisnis** (fokus value, bahasa non-teknis).

**Current Project State:**
- Laravel 13.8 + PHP 8.5
- Stack: Livewire 4, Flux 2, Laravel Fortify
- Database: MySQL (already configured)
- Redis available
- Only default User model exists — greenfield project

---

## Phase 1: Level Biasa (Basic POS)

### 1A. PRD — Developer Version

**Fitur Utama:**
1. **Manajemen Produk** — CRUD produk (nama, harga, stok, SKU)
2. **Transaksi POS** — Kasir cepat dengan pilih produk, hitung total, terima pembayaran
3. **Manajemen Kategori** — Kategorisasi produk sederhana
4. **Laporan Harian** — Rekap penjualan per hari
5. **Manajemen Pengguna** — Role: admin dan kasir (dengan Fortify)

**User Stories:**
- Sebagai kasir, saya ingin memilih produk dan memproses pembayaran dengan cepat
- Sebagai admin, saya ingin menambah/mengubah produk dan kategorinya
- Sebagai admin, saya ingin melihat laporan penjualan harian
- Sebagai pengguna, saya ingin login dengan aman

**Functional Requirements:**
- FR-01: Sistem dapat menampilkan daftar produk dengan pencarian
- FR-02: Sistem dapat memproses transaksi dengan minimal 1 item
- FR-03: Sistem dapat menghitung total dan kembalian secara otomatis
- FR-04: Sistem menyimpan riwayat transaksi
- FR-05: Sistem mendukung role admin dan kasir
- FR-06: Sistem dapat menampilkan laporan penjualan harian

**Non-Functional Requirements:**
- NFR-01: Response time transaksi < 2 detik
- NFR-02: Tersedia dalam Bahasa Indonesia
- NFR-03: Single-tenant (1 toko = 1 instance)
- NFR-04: Cetak struk sederhana (thermal printer / browser print)

**Constraints:**
- Hanya 1 toko per instance
- Tidak ada manajemen stok multi-gudang
- Tidak ada pelanggan tersimpan
- Pembayaran tunai saja

**Dependency Antar Fitur:**
```
Produk & Kategori → Transaksi POS → Laporan Harian
       ↑
  Manajemen User (auth)
```

**Teknologi/Library Rekomendasi:**
| Library | Fungsi |
|---------|--------|
| Laravel 13 + Livewire 4 | Framework utama & frontend reaktif |
| Flux 2 | UI component library |
| Laravel Fortify | Authentication (existing) |
| barryvdh/laravel-dompdf | Cetak struk PDF |
| Laravel Excel (maatwebsite) | Export laporan ke Excel (opsional) |

---

### 1B. ERD — Developer Version

**Entities & Tables:**

**1. `categories`**
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| name | VARCHAR(100) | NOT NULL, UNIQUE |
| slug | VARCHAR(120) | NOT NULL, UNIQUE, INDEX |
| description | TEXT | NULLABLE |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**2. `products`**
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| category_id | BIGINT UNSIGNED | FK → categories.id, INDEX |
| name | VARCHAR(200) | NOT NULL |
| slug | VARCHAR(220) | NOT NULL, UNIQUE |
| sku | VARCHAR(50) | NOT NULL, UNIQUE, INDEX |
| price | DECIMAL(12,2) | NOT NULL, DEFAULT 0 |
| stock | INTEGER | NOT NULL, DEFAULT 0 |
| description | TEXT | NULLABLE |
| is_active | BOOLEAN | DEFAULT true, INDEX |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**3. `transactions`**
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| user_id | BIGINT UNSIGNED | FK → users.id, INDEX |
| invoice_number | VARCHAR(50) | NOT NULL, UNIQUE |
| total_amount | DECIMAL(12,2) | NOT NULL |
| paid_amount | DECIMAL(12,2) | NOT NULL |
| change_amount | DECIMAL(12,2) | NOT NULL DEFAULT 0 |
| payment_method | ENUM('cash') | NOT NULL, DEFAULT 'cash' |
| notes | TEXT | NULLABLE |
| created_at | TIMESTAMP | INDEX |
| updated_at | TIMESTAMP | |

**4. `transaction_items`**
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| transaction_id | BIGINT UNSIGNED | FK → transactions.id, INDEX |
| product_id | BIGINT UNSIGNED | FK → products.id |
| quantity | INTEGER | NOT NULL, > 0 |
| unit_price | DECIMAL(12,2) | NOT NULL |
| subtotal | DECIMAL(12,2) | NOT NULL (quantity * unit_price) |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Relasi:**
- `categories` 1──N `products`
- `products` 1──N `transaction_items`
- `transactions` 1──N `transaction_items`
- `users` 1──N `transactions`

**Indexes Required:**
- products: `category_id`, `sku` (unique), `is_active`, `name` (fulltext optional)
- transactions: `user_id`, `created_at`, `invoice_number` (unique)
- transaction_items: `transaction_id`, `product_id`
- categories: `slug` (unique), `name` (unique)

---

### 1C. PRD — Stakeholder Version

**Fitur Utama (Business View):**

1. **Katalog Produk Digital** — Simpan data barang dagangan (nama, harga, stok) dengan kategori
2. **Kasir Digital** — Antar muka kasir cepat untuk proses transaksi sehari-hari
3. **Atur Stok Barang** — Pantau stok barang secara real-time
4. **Laporan Penjualan Harian** — Lihat total penjualan harian dengan mudah
5. **Keamanan Akses** — Admin dan kasir memiliki akses berbeda

**Use Cases Bisnis:**
- Kasir dapat memilih barang, melihat total, dan menyelesaikan transaksi dalam hitungan detik
- Pemilik toko dapat menambah barang baru ke katalog kapan saja
- Pemilik toko dapat melihat berapa total penjualan hari ini tanpa hitung manual
- Stok barang otomatis berkurang saat transaksi selesai

**Business Requirements:**
- BR-01: Proses transaksi tidak boleh lebih dari 30 detik per pelanggan
- BR-02: Data penjualan harus akurat hingga senilai terkecil
- BR-03: Hanya kasir yang berwenang yang bisa memproses transaksi
- BR-04: Stok barang tidak boleh minus (wajib cek ketersediaan)

**Success Metrics:**
- Waktu proses transaksi rata-rata < 10 detik
- Zero error perhitungan total/kembalian
- 100% data penjualan terekam dan bisa diaudit

**Constraints:**
- Cocok untuk toko dengan 1 lokasi saja
- Pembayaran tunai di tempat (cash only)
- Cetak struk dari printer thermal / kertas biasa

---

### 1D. ERD — Stakeholder Version

**Entitas Utama:**

```
[Kategori] ───1─→N─── [Produk]
                           │
                           │
                           N
                           │
                    [Item Transaksi] ───N───1─── [Transaksi]
                                                      │
                                                      │1
                                                      │
                                                   [Pengguna]
```

**Penjelasan Data Flow:**
1. **Kategori** → Mengelompokkan barang dagangan (contoh: Makanan, Minuman, Snack)
2. **Produk** → Data barang yang dijual (nama, harga, stok)
3. **Transaksi** → Satu struk pembelian (nomor invoice, total, pembayaran)
4. **Item Transaksi** → Baris per barang dalam satu transaksi
5. **Pengguna** → Kasir atau admin yang memproses transaksi

**Alur data bisnis:**
```
Kategori → Produk → Keranjang (via Item Transaksi) → Transaksi → Laporan
```

---

## Phase 2: Level Standar (Intermediate POS)

### 2A. PRD — Developer Version

**Fitur Tambahan (dari Level Biasa):**
1. **Manajemen Pelanggan** — Data pelanggan tersimpan (nama, telepon, poin)
2. **Pembayaran Multi-Metode** — Tunai, transfer, QRIS, debit/kredit
3. **Manajemen Diskon & Promo** — Diskon per item/persen, promo periode tertentu
4. **Manajemen Supplier** — Data pemasok barang
5. **Manajemen Stok Lanjutan** — Stok masuk (purchase order), stok opname, minimum stock alert
6. **Multi-User dengan Permission Detail** — Permission per fitur (manage_products, view_reports, dll)
7. **Laporan Laba-Rugi** — Laporan keuangan sederhana
8. **Cetak Struk Custom** — Template struk dengan logo toko

**New User Stories:**
- Sebagai admin, saya ingin mendaftarkan pelanggan setia dan melihat riwayat belanjanya
- Sebagai kasir, saya ingin memproses pembayaran nontunai (QRIS/transfer)
- Sebagai admin, saya ingin membuat diskon khusus untuk produk tertentu
- Sebagai admin, saya ingin mencatat stok masuk dari supplier
- Sebagai pemilik, saya ingin melihat laporan laba-rugi bulanan

**Functional Requirements (additional):**
- FR-07: Sistem menyimpan data pelanggan (nama, kontak, poin)
- FR-08: Sistem mendukung pembayaran tunai, QRIS, dan transfer
- FR-09: Sistem dapat menerapkan diskon per item atau per transaksi
- FR-10: Sistem mencatat pembelian stok dari supplier
- FR-11: Sistem memberi alert saat stok di bawah minimum
- FR-12: Sistem menghitung HPP dan laba-rugi
- FR-13: Sistem memiliki permission terperinci (gate/ policy)

**Non-Functional Requirements (additional):**
- NFR-05: Support untuk printer thermal 58mm dan 80mm
- NFR-06: Waktu cetak struk < 3 detik
- NFR-07: Data pelanggan dienkripsi (nomor telepon)

**Teknologi/Library Tambahan:**
| Library | Fungsi |
|---------|--------|
| Spatie/laravel-permission | Role & permission management |
| Laravel Tips/Wallet (opsional) | Poin/loyalty system |
| mike42/escpos-php | Cetak ke printer thermal |

**Migration Path dari Level Biasa:**
- Tambah tabel: customers, suppliers, purchase_orders, purchase_order_items, stock_opnames, discounts, discount_product
- Modifikasi: transactions (add customer_id, payment_method jadi enum multi)
- Add columns: products (purchase_price untuk HPP, minimum_stock)

---

### 2B. ERD — Developer Version

**New Tables (additional from Level Basic):**

**5. `customers`**
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| name | VARCHAR(150) | NOT NULL |
| phone | VARCHAR(20) | NULLABLE, UNIQUE, INDEX |
| email | VARCHAR(100) | NULLABLE |
| address | TEXT | NULLABLE |
| poin | INTEGER | NOT NULL DEFAULT 0 |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**6. `suppliers`**
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| name | VARCHAR(150) | NOT NULL |
| phone | VARCHAR(20) | NULLABLE |
| email | VARCHAR(100) | NULLABLE |
| address | TEXT | NULLABLE |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**7. `purchase_orders`**
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| user_id | BIGINT UNSIGNED | FK → users.id |
| supplier_id | BIGINT UNSIGNED | FK → suppliers.id |
| po_number | VARCHAR(50) | NOT NULL, UNIQUE |
| status | ENUM('pending','received','cancelled') | NOT NULL, DEFAULT 'pending', INDEX |
| total_amount | DECIMAL(12,2) | NOT NULL |
| notes | TEXT | NULLABLE |
| received_at | TIMESTAMP | NULLABLE |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**8. `purchase_order_items`**
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| purchase_order_id | BIGINT UNSIGNED | FK → purchase_orders.id, INDEX |
| product_id | BIGINT UNSIGNED | FK → products.id |
| quantity | INTEGER | NOT NULL |
| unit_cost | DECIMAL(12,2) | NOT NULL |
| subtotal | DECIMAL(12,2) | NOT NULL |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**9. `discounts`**
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| name | VARCHAR(100) | NOT NULL |
| type | ENUM('percentage','nominal') | NOT NULL |
| value | DECIMAL(12,2) | NOT NULL |
| start_date | DATETIME | NOT NULL, INDEX |
| end_date | DATETIME | NOT NULL |
| is_active | BOOLEAN | DEFAULT true |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**10. `discount_product` (pivot)**
| Column | Type | Constraints |
|--------|------|-------------|
| discount_id | BIGINT UNSIGNED | PK, FK → discounts.id |
| product_id | BIGINT UNSIGNED | PK, FK → products.id |

**11. `stock_opnames`**
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| user_id | BIGINT UNSIGNED | FK → users.id |
| date | DATE | NOT NULL, INDEX |
| notes | TEXT | NULLABLE |
| status | ENUM('draft','completed') | DEFAULT 'draft' |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**12. `stock_opname_items`**
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| stock_opname_id | BIGINT UNSIGNED | FK → stock_opnames.id |
| product_id | BIGINT UNSIGNED | FK → products.id |
| system_stock | INTEGER | NOT NULL |
| actual_stock | INTEGER | NOT NULL |
| difference | INTEGER | NOT NULL |
| notes | TEXT | NULLABLE |

**Modified Tables:**

**`products` — additional columns:**
| purchase_price | DECIMAL(12,2) | NULLABLE |
| minimum_stock | INTEGER | NOT NULL DEFAULT 0 |

**`transactions` — additional columns:**
| customer_id | BIGINT UNSIGNED | NULLABLE, FK → customers.id, INDEX |
| payment_method | ENUM('cash','qris','transfer','debit','credit') | NOT NULL |
| discount_id | BIGINT UNSIGNED | NULLABLE, FK → discounts.id |
| discount_amount | DECIMAL(12,2) | NOT NULL DEFAULT 0 |

**Relasi Baru:**
- `suppliers` 1──N `purchase_orders`
- `purchase_orders` 1──N `purchase_order_items` → N──1 `products`
- `discounts` M──N `products` (via `discount_product`)
- `stock_opnames` 1──N `stock_opname_items` → N──1 `products`
- `transactions` N──1 `customers` (opsional)

**Indexes Additional:**
- customers: `phone` (unique)
- purchase_orders: `supplier_id`, `status`, `po_number` (unique)
- purchase_order_items: `purchase_order_id`, `product_id`
- stock_opnames: `date`, `user_id`
- discounts: `start_date`, `is_active`

---

### 2C. PRD — Stakeholder Version

**Fitur Tambahan (Business View):**

1. **Data Pelanggan & Poin** — Simpan data pembeli, beri poin untuk setiap transaksi
2. **Pembayaran Fleksibel** — Terima pembayaran QRIS, transfer, atau kartu
3. **Program Diskon & Promo** — Buat diskon spesial untuk produk tertentu atau periode tertentu
4. **Kelola Pemasok** — Catat data supplier dan riwayat pembelian barang
5. **Atur Stok Masuk** — Catat barang datang dari supplier dengan rapi
6. **Peringatan Stok Minimal** — Notifikasi jika stok mau habis
7. **Laporan Laba-Rugi** — Lihat keuntungan bersih setelah dikurangi modal barang
8. **Cetak Struk Dengan Logo** — Struk terlihat lebih profesional

**Use Cases Bisnis:**
- Pemilik toko dapat memberikan poin untuk pelanggan setia
- Pelanggan bisa bayar pakai QRIS/transfer (tidak harus tunai)
- Pemilik toko bisa membuat promo "Beli 2 gratis 1" atau diskon 10%
- Admin mencatat barang masuk dari supplier dengan harga modal
- Pemilik toko dapat melihat laba bersih per bulan

**Business Requirements:**
- BR-05: Pelanggan bisa diidentifikasi via nomor telepon
- BR-06: Metode pembayaran minimal 3 (tunai, QRIS, transfer)
- BR-07: Diskon dapat dibatasi periode waktu tertentu
- BR-08: Stok minimal harus bisa diatur per produk

**Success Metrics (Additional):**
- 50%+ transaksi menggunakan pelanggan terdaftar
- Stok minus = 0 (zero negative stock)
- Laporan laba-rugi akurat dalam 1 klik

---

### 2D. ERD — Stakeholder Version

**Entitas Tambahan:**

```
[Supplier] ───1───N─── [Purchase Order] ───1───N─── [PO Item] ───N───1─── [Produk]
                                                                              │
                                                          [Diskon] ───M───N──┘
                                                                              │
[Pelanggan] ───1───N─── [Transaksi] ───1───N─── [Item Transaksi] ───N───1───┘
                              │
                              │  (metode pembayaran: tunai/qris/transfer/dll)
```

**Simplified Data Flow:**
```
Supplier → Beli Stok (PO) → Gudang (Produk) → Kasir (Transaksi) → Pelanggan
                              │                                       │
                          [Stok Opname]                        [Poin Pelanggan]
```

**Penjelasan Flow Baru:**
- **PO/Pembelian**: Barang masuk dari supplier → stok bertambah
- **Diskon**: Bisa dipasang ke produk tertentu
- **Pelanggan**: Data pembeli tersimpan, dapat poin dari transaksi
- **Stok Opname**: Cek fisik vs sistem untuk akurasi stok

---

## Phase 3: Level Kompleks (Enterprise POS)

### 3A. PRD — Developer Version

**Fitur Tambahan (dari Level Standar):**

1. **Multi-Tenant** — Satu instance melayani banyak toko/bisnis
2. **Multi-Warehouse** — Stok terpisah per gudang/lokasi
3. **Multi-Outlet** — Satu pemilik dengan banyak cabang toko
4. **Manajemen Pajak (PPN)** — Perhitungan pajak otomatis per item
5. **Laporan Keuangan Lengkap** — Arus kas, neraca, laba-rugi periodik
6. **Manajemen Shift Kasir** — Buka/tutup shift, setoran kasir
7. **Integrasi API** — REST API untuk integrasi eksternal (e-commerce, akuntansi)
8. **Pembayaran Split & Partial** — Bayar dengan multi-metode dalam 1 transaksi
9. **Hutang & Piutang** — Transaksi hutang (credit) untuk pelanggan korporat
10. **Notifikasi & Alert** — Stok menipis, hutang jatuh tempo, dll (email/notif in-app)
11. **Audit Trail Lengkap** — Semua perubahan data tercatat (siapa, kapan, apa)
12. **Backup & Restore** — Backup database terjadwal
13. **Manajemen Harga Jual** — Harga berbeda per cabang/grup pelanggan

**New User Stories:**
- Sebagai pemilik usaha dengan 3 cabang, saya ingin melihat laporan gabungan semua cabang
- Sebagai pemilik, saya ingin stok dipisah antar gudang dan bisa transfer stok antar gudang
- Sebagai admin, saya ingin mengatur harga jual berbeda untuk setiap cabang
- Sebagai manajer, saya ingin membuka shift kasir dan menerima setoran di akhir shift
- Sebagai customer service, saya bisa membuat transaksi dengan piutang untuk pelanggan korporat
- Sebagai akuntan, saya ingin export data ke software akuntansi via API
- Sebagai auditor, saya ingin melihat siapa yang mengubah harga produk dan kapan

**Functional Requirements (additional):**
- FR-14: Sistem mendukung banyak tenant dengan data terisolasi
- FR-15: Sistem mendukung banyak gudang dengan transfer stok antar gudang
- FR-16: Sistem menghitung PPN otomatis per item atau per transaksi
- FR-17: Sistem menyediakan REST API dengan token authentication
- FR-18: Sistem mencatat semua aktivitas perubahan data (audit trail)
- FR-19: Sistem mendukung pembayaran split (tunai + QRIS dalam 1 transaksi)
- FR-20: Sistem memiliki fitur buka/tutup shift kasir
- FR-21: Sistem mencatat hutang & piutang lengkap dengan jatuh tempo
- FR-22: Sistem mendukung backup database terjadwal

**Non-Functional Requirements (additional):**
- NFR-08: Isolasi tenant ketat (satu tenant tidak bisa akses data tenant lain)
- NFR-09: Response time rata-rata < 1 detik (dengan caching Redis)
- NFR-10: Availability 99.9% (minimal)
- NFR-11: API rate limiting per tenant
- NFR-12: Semua transaksi keuangan immutable (tidak bisa dihapus/diedit)
- NFR-13: Mendukung 100+ pengguna bersamaan

**Constraints:**
- Memerlukan Redis untuk queue dan caching
- Memerlukan cron job untuk backup dan notifikasi
- Storage bertambah signifikan karena audit trail
- Kompleksitas permission management meningkat

**Teknologi/Library Tambahan:**
| Library | Fungsi |
|---------|--------|
| Spatie/laravel-permission (extended) | Multi-tenant roles |
| stancl/tenancy | Multi-tenant database isolation |
| Laravel Sanctum | API token authentication |
| Laravel Horizon | Queue monitoring (Redis) |
| OwenIt/laravel-auditing | Audit trail |
| Spatie/laravel-backup | Database & file backup |
| Laravel Notification + Database | In-app & email notifikasi |
| barryvdh/laravel-dompdf / laravel-snappy | Laporan PDF |
| Laravel Excel | Export/import kompleks |

**Migration Path dari Level Standar:**
- Major schema change: tambah `tenant_id` atau separate database per tenant
- Restructure: `warehouses`, `outlets`, `shifts`, `debts`, `receivables`
- Add: `audit_logs`, `api_tokens`, `notifications` tables
- Modifikasi: hampir semua tabel existing ditambah tenant_id

---

### 3B. ERD — Developer Version

**New Tables (additional from Level Standard):**

**13. `tenants`** (jika single-database approach)
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| name | VARCHAR(150) | NOT NULL |
| slug | VARCHAR(100) | UNIQUE |
| domain | VARCHAR(100) | NULLABLE, UNIQUE |
| database | VARCHAR(100) | NULLABLE (jika separate DB) |
| settings | JSON | NULLABLE |
| is_active | BOOLEAN | DEFAULT true |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**14. `outlets`**
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| tenant_id | BIGINT UNSIGNED | FK → tenants.id, INDEX |
| name | VARCHAR(150) | NOT NULL |
| code | VARCHAR(20) | NOT NULL, UNIQUE |
| address | TEXT | NULLABLE |
| phone | VARCHAR(20) | NULLABLE |
| is_active | BOOLEAN | DEFAULT true |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**15. `warehouses`**
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| tenant_id | BIGINT UNSIGNED | FK → tenants.id, INDEX |
| outlet_id | BIGINT UNSIGNED | NULLABLE, FK → outlets.id |
| name | VARCHAR(150) | NOT NULL |
| code | VARCHAR(20) | NOT NULL, UNIQUE |
| address | TEXT | NULLABLE |
| is_active | BOOLEAN | DEFAULT true |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**16. `stock_movements`** (menggantikan stock simple)
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| product_id | BIGINT UNSIGNED | FK → products.id, INDEX |
| warehouse_id | BIGINT UNSIGNED | FK → warehouses.id, INDEX |
| user_id | BIGINT UNSIGNED | FK → users.id |
| type | ENUM('in','out','transfer_in','transfer_out','adjustment') | NOT NULL, INDEX |
| reference_type | VARCHAR(50) | NULLABLE (polymorphic: transaction, purchase_order, etc) |
| reference_id | BIGINT UNSIGNED | NULLABLE |
| quantity | INTEGER | NOT NULL |
| stock_before | INTEGER | NOT NULL |
| stock_after | INTEGER | NOT NULL |
| notes | TEXT | NULLABLE |
| created_at | TIMESTAMP | |

**17. `shifts`**
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| user_id | BIGINT UNSIGNED | FK → users.id, INDEX |
| outlet_id | BIGINT UNSIGNED | FK → outlets.id, INDEX |
| opened_at | DATETIME | NOT NULL |
| closed_at | DATETIME | NULLABLE |
| opening_balance | DECIMAL(12,2) | NOT NULL DEFAULT 0 |
| closing_balance | DECIMAL(12,2) | NULLABLE |
| expected_balance | DECIMAL(12,2) | NULLABLE |
| cash_sales | DECIMAL(12,2) | NULLABLE |
| non_cash_sales | DECIMAL(12,2) | NULLABLE |
| difference | DECIMAL(12,2) | NULLABLE |
| status | ENUM('open','closed','reconciled') | DEFAULT 'open', INDEX |
| notes | TEXT | NULLABLE |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**18. `debts`**
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| tenant_id | BIGINT UNSIGNED | FK → tenants.id |
| customer_id | BIGINT UNSIGNED | FK → customers.id, INDEX |
| transaction_id | BIGINT UNSIGNED | NULLABLE, FK → transactions.id |
| type | ENUM('receivable','payable') | NOT NULL, INDEX |
| total_amount | DECIMAL(12,2) | NOT NULL |
| paid_amount | DECIMAL(12,2) | NOT NULL DEFAULT 0 |
| due_date | DATE | NOT NULL, INDEX |
| status | ENUM('pending','partial','paid','overdue') | DEFAULT 'pending', INDEX |
| notes | TEXT | NULLABLE |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**19. `debt_payments`**
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| debt_id | BIGINT UNSIGNED | FK → debts.id, INDEX |
| user_id | BIGINT UNSIGNED | FK → users.id |
| amount | DECIMAL(12,2) | NOT NULL |
| payment_method | ENUM('cash','transfer','qris') | NOT NULL |
| paid_at | DATETIME | NOT NULL |
| notes | TEXT | NULLABLE |
| created_at | TIMESTAMP | |

**20. `taxes`**
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| tenant_id | BIGINT UNSIGNED | FK → tenants.id |
| name | VARCHAR(100) | NOT NULL (e.g. "PPN 11%") |
| rate | DECIMAL(5,2) | NOT NULL (e.g. 11.00) |
| is_active | BOOLEAN | DEFAULT true |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**21. `product_taxes` (pivot)**
| Column | Type | Constraints |
|--------|------|-------------|
| product_id | BIGINT UNSIGNED | PK, FK → products.id |
| tax_id | BIGINT UNSIGNED | PK, FK → taxes.id |

**22. `product_prices`** (harga per cabang/grup)
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| product_id | BIGINT UNSIGNED | FK → products.id, INDEX |
| outlet_id | BIGINT UNSIGNED | NULLABLE, FK → outlets.id |
| customer_group | VARCHAR(50) | NULLABLE ('retail','wholesale','corporate') |
| price | DECIMAL(12,2) | NOT NULL |
| is_active | BOOLEAN | DEFAULT true |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**23. `audit_logs`**
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| user_id | BIGINT UNSIGNED | NULLABLE, FK → users.id |
| tenant_id | BIGINT UNSIGNED | NULLABLE, INDEX |
| auditable_type | VARCHAR(100) | NOT NULL, INDEX |
| auditable_id | BIGINT UNSIGNED | NOT NULL, INDEX |
| event | VARCHAR(20) | NOT NULL ('created','updated','deleted') |
| old_values | JSON | NULLABLE |
| new_values | JSON | NULLABLE |
| url | VARCHAR(500) | NULLABLE |
| ip_address | VARCHAR(45) | NULLABLE |
| user_agent | TEXT | NULLABLE |
| created_at | TIMESTAMP | INDEX |

**Modified Tables (Semua existing — tambah tenant_id):**
- `products` → +`tenant_id`, +`warehouse_id` (primary warehouse)
- `categories` → +`tenant_id`
- `customers` → +`tenant_id`
- `suppliers` → +`tenant_id`
- `transactions` → +`tenant_id`, +`outlet_id`, +`shift_id`, +`tax_id`, split payment data
- `transaction_items` → +`tax_id`, +`tax_rate`, +`tax_amount`
- `discounts` → +`tenant_id`, +`outlet_id` (nullable)
- `purchase_orders` → +`tenant_id`, +`warehouse_id`
- `users` → +`tenant_id` (FK), +`outlet_id` (default outlet)

**Split Payment Support:**

**`transaction_payments` (new)**
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PK, Auto Increment |
| transaction_id | BIGINT UNSIGNED | FK → transactions.id, INDEX |
| payment_method | ENUM('cash','qris','transfer','debit','credit') | NOT NULL |
| amount | DECIMAL(12,2) | NOT NULL |
| reference | VARCHAR(100) | NULLABLE (no. referensi transfer) |
| created_at | TIMESTAMP | |

**Relasi Kompleks:**
- `tenants` 1──N `outlets` 1──N `warehouses`
- `tenants` 1──N `users`
- `warehouses` 1──N `stock_movements` (source)
- `outlets` 1──N `shifts` 1──N `transactions`
- `transactions` 1──N `transaction_payments` (split payment)
- `customers` 1──N `debts` 1──N `debt_payments`
- `products` M──N `taxes`
- `products` 1──N `product_prices` N──1 `outlets`

**Indexes Additional (Complex level):**
- Semua tabel: `tenant_id` sebagai first index
- audit_logs: composite `(auditable_type, auditable_id, created_at)`
- stock_movements: `(product_id, warehouse_id, type)`, `reference_type + reference_id`
- debts: `(customer_id, status, due_date)` composite
- shifts: `(user_id, opened_at)`, `(outlet_id, status)`
- product_prices: `(product_id, outlet_id)` unique composite

---

### 3C. PRD — Stakeholder Version

**Fitur Tambahan (Business View):**

1. **Satu Sistem untuk Banyak Toko** — Kelola beberapa cabang toko dari satu dashboard
2. **Stok Terpusat Multi-Gudang** — Stok dipisah per gudang, bisa transfer stok antar gudang
3. **Harga Berbeda Tiap Cabang** — Atur harga jual berbeda untuk setiap cabang/grup pelanggan
4. **Pajak Otomatis (PPN)** — Hitung pajak otomatis sesuai produk
5. **Shift Kasir Terkelola** — Setiap kasir buka shift, setoran tercatat rapi
6. **Hutang & Piutang** — Jual dengan sistem tempo untuk pelanggan korporat
7. **Pembayaran Split** — Bayar dengan kombinasi tunai + QRIS dalam 1 transaksi
8. **API untuk Integrasi** — Hubungkan dengan e-commerce, software akuntansi, dll
9. **Audit Trail** — Semua perubahan tercatat untuk keperluan audit
10. **Backup Otomatis** — Data dibackup otomatis setiap hari
11. **Laporan Keuangan Lengkap** — Neraca, arus kas, laba-rugi per cabang & konsolidasi

**Use Cases Bisnis:**
- Pemilik 3 cabang toko melihat laporan keuangan konsolidasi dari semua cabang
- Stok dipisah per gudang, barang bisa dipindahkan antar gudang dengan catatan
- Pelanggan korporat bisa beli dengan invoice tempo 30 hari
- Akuntan perusahaan mengekspor data ke software akuntansi via API
- Auditor melihat riwayat perubahan harga produk 3 bulan lalu

**Business Requirements:**
- BR-09: Setiap cabang memiliki data transaksi terpisah tapi terlihat terpusat
- BR-10: Harga jual bisa berbeda per cabang (misal: cabang mall lebih mahal)
- BR-11: PPN dihitung otomatis sesuai peraturan terbaru
- BR-12: Hutang piutang termonitor dengan jatuh tempo dan denda otomatis
- BR-13: Data tidak bisa diubah setelah transaksi selesai (immutable)
- BR-14: Backup data minimal 1x per hari

**Success Metrics (Additional):**
- 100% cabang terintegrasi dalam 1 sistem
- Zero data loss (backup validated)
- Waktu tutup buku akhir bulan < 1 jam

---

### 3D. ERD — Stakeholder Version

**Simplified Architecture:**

```
                          ┌─────────────────┐
                          │   TENANT/PERUSAHAAN  │
                          └─────────────────┘
                                   │
                    ┌──────────────┼──────────────┐
                    │              │              │
              ┌──────────┐  ┌──────────┐  ┌──────────┐
              │ CABANG 1 │  │ CABANG 2 │  │ CABANG 3 │
              └──────────┘  └──────────┘  └──────────┘
                    │              │              │
              ┌──────────┐  ┌──────────┐  ┌──────────┐
              │ GUDANG A │  │ GUDANG B │  │ GUDANG C │
              └──────────┘  └──────────┘  └──────────┘
                    │
         ┌──────────┼──────────┐
         │          │          │
    [Shift]   [Transaksi]   [Stok]
    [Kasir]   [Pembayaran]  [Masuk/Keluar]
              [Hutang/Piutang]
```

**Data Flow Bisnis Level Kompleks:**

```
Supplier → PO → Gudang(Cabang) → Kasir(Shift) → Transaksi → Pelanggan
                              │                              │
                    [Transfer Stok]                  [Split Payment]
                    [Stok Opname]                   [Hutang Tempo]
                                                         │
                                                    [Laporan Keuangan]
                                                    [Audit Trail]
```

**Penjelasan Flow:**
- **Tenant** → Satu perusahaan dengan banyak cabang
- **Cabang (Outlet)** → Setiap cabang punya gudang dan kasir sendiri
- **Shift** → Kasir buka shift, transaksi tercatat dalam shift tersebut
- **Stok** → Tercatat per gudang, bisa dipindahkan (transfer)
- **Pembayaran Split** → 1 transaksi bisa bayar dengan 2 metode (misal: tunai 50rb + QRIS 30rb)
- **Hutang/Piutang** → Untuk pelanggan korporat dengan jatuh tempo
- **Audit Trail** → Semua perubahan data terekam otomatis

---

## Implementation Roadmap

```
Level Biasa (1-2 months)
    │
    ├── Sprint 1: Auth & User Management (1 week)
    ├── Sprint 2: Products & Categories (2 weeks)
    ├── Sprint 3: POS Transactions (2 weeks)
    ├── Sprint 4: Daily Reports & Printing (1 week)
    │
    ▼
Level Standar (2-3 months)
    │
    ├── Sprint 5: Customers & Loyalty (1 week)
    ├── Sprint 6: Multi-Payment & Discounts (2 weeks)
    ├── Sprint 7: Suppliers & Purchase Orders (2 weeks)
    ├── Sprint 8: Advanced Stock & Opname (1 week)
    ├── Sprint 9: Profit/Loss Reports & Permissions (2 weeks)
    │
    ▼
Level Kompleks (3-5 months)
    │
    ├── Sprint 10: Multi-Tenant Infrastructure (3 weeks)
    ├── Sprint 11: Multi-Warehouse & Stock Movements (2 weeks)
    ├── Sprint 12: Outlets & Shift Management (2 weeks)
    ├── Sprint 13: Taxes & Split Payment (2 weeks)
    ├── Sprint 14: Debts & Receivables (2 weeks)
    ├── Sprint 15: REST API & Integrations (3 weeks)
    ├── Sprint 16: Audit Trail, Notifications & Backup (2 weeks)
    ├── Sprint 17: Advanced Reporting (2 weeks)
    └── Sprint 18: Testing, Optimization & Deployment (3 weeks)
```

---

## Risks & Mitigation

| Risk | Level | Mitigation |
|------|-------|------------|
| Scope creep pada Level Biasa (ingin fitur standar) | HIGH | Strict feature freeze per level, documented roadmap |
| Kinerja memburuk saat multi-tenant | HIGH | Gunakan separate database per tenant atau query scoping ketat; cache Redis |
| Kompleksitas permission management | MEDIUM | Pakai Spatie/laravel-permission sejak Level Standar |
| Data inconsistency antar gudang/cabang | MEDIUM | Stock movements immutable, wajib reference ke transaksi |
| Migrasi data saat upgrade level | HIGH | Desain database Level Biasa sudah antisipasi kolom future (nullable FK) |
| Konfigurasi printer thermal | LOW | Abstraksi printing dengan interface agar bisa multiple driver |

---

## Estimated Complexity: **HIGH** (overall 3-level system)

- **Level Biasa**: Low-Medium — ~4-6 sprints, ~125 person-days
- **Level Standar**: Medium — ~5-7 sprints, ~175 person-days
- **Level Kompleks**: High — ~9-12 sprints, ~300 person-days
- **Total**: ~600 person-days (depending on team size)

**WAITING FOR CONFIRMATION**: Proceed with this plan? (yes/no/modify)

---

*Dokumen ini mencakup PRD dan ERD untuk ketiga level dengan dua versi audiens (Developer & Stakeholder), lengkap dengan tabel, relasi, index, roadmap, dan rekomendasi teknologi.*
