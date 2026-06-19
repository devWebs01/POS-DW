# BAB IV - PERANCANGAN DAN IMPLEMENTASI SISTEM

Dokumentasi ini adalah **pendukung** untuk file-file utama Bab IV yang berada di direktori `docs/`. File ini berisi informasi tentang assets (diagram, tabel, gambar).

---

## Struktur Folder

```
docs/
├── BAB-IV-PERANCANGAN-DAN-IMPLEMENTASI.md  ← Indeks utama
├── 4.0-analisis-sistem.md                 ← Sub-bab 4.0 (Analisis Sistem)
├── 4.1-analisis-kebutuhan-sistem.md        ← Sub-bab 4.1 (Analisa Kebutuhan Sistem)
├── 4.2-perancangan-sistem.md               ← Sub-bab 4.2 (Analisis Proses Sistem)
├── 4.3-perancangan-basis-data.md           ← Sub-bab 4.3 (Perancangan Perangkat Lunak)
├── 4.4-perancangan-antarmuka.md            ← Sub-bab 4.4 (Perancangan Antarmuka)
├── 4.5-implementasi-sistem.md              ← Sub-bab 4.5 (Implementasi Sistem)
├── 4.6-pengujian-sistem.md                 ← Sub-bab 4.6 (Pengujian Sistem)
└── bab-iv/
    ├── diagrams/           ← Diagram PlantUML (10 files)
    ├── images/             ← Screenshot aplikasi
    └── tables/             ← Tabel-tabel pendukung
```

## File Utama (di `docs/`)

| File | Status | Deskripsi |
|------|--------|-----------|
| `BAB-IV-PERANCANGAN-DAN-IMPLEMENTASI.md` | ✅ Selesai | Indeks utama dan daftar isi |
| `4.0-analisis-sistem.md` | ✅ Selesai | Analisis profil objek, sistem berjalan, solusi permasalahan |
| `4.1-analisis-kebutuhan-sistem.md` | ✅ Selesai | Analisa kebutuhan fungsional (35) dan non-fungsional (30), spesifikasi HW/SW |
| `4.2-perancangan-sistem.md` | ✅ Selesai | Use Case, Deskripsi Use Case per aktor, Activity, Sequence, Class Diagram, Analisis Input/Output |
| `4.3-perancangan-basis-data.md` | ✅ Selesai | ERD dan Struktur Tabel Database (13 tabel) |
| `4.4-perancangan-antarmuka.md` | ✅ Selesai | Rancangan Struktur Program, mockup 8 halaman utama |
| `4.5-implementasi-sistem.md` | ✅ Selesai | Database, Program, Tampilan |
| `4.6-pengujian-sistem.md` | ✅ Selesai | Black Box Testing (35 skenario), Hasil |

## Diagram Tersedia (di `diagrams/`)

| File | Tipe | Deskripsi |
|------|------|-----------|
| `flowchart-sistem.puml` | Flowchart | Alur sistem keseluruhan |
| `usecase-diagram.puml` | Use Case | 23 use case, 3 aktor |
| `activity-login.puml` | Activity | Proses login dengan 2FA |
| `activity-transaksi.puml` | Activity | Proses transaksi POS |
| `activity-laporan.puml` | Activity | Generate dan export laporan |
| `sequence-login.puml` | Sequence | Interaksi login (6 objek) |
| `sequence-transaksi.puml` | Sequence | Interaksi transaksi (6 objek) |
| `sequence-laporan.puml` | Sequence | Interaksi generate laporan |
| `class-diagram.puml` | Class | 8 class model + relasi |
| `erd.puml` | ERD | 10 entitas database |

## Cara Render Diagram

```bash
# Menggunakan Java (PlantUML standalone)
java -jar plantuml.jar docs/bab-iv/diagrams/*.puml

# Atau via VS Code: Install extension "PlantUML"
# Buka file .puml, tekan Alt+D untuk preview

# Atau via online: https://www.plantuml.com/plantuml/uml/
```

---

## Catatan

- **Format output final:** Copy dari Markdown ke Word/LaTeX dengan penyesuaian template kampus
- **Screenshot:** Ambil dari aplikasi yang running, simpan di `images/`
- **Diagram:** Generate PNG dari file .puml untuk dimasukkan ke Word
