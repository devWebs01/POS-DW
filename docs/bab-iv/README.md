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
| `flowchart-sistem.mmd` | Flowchart | Alur sistem keseluruhan |
| `usecase-diagram.mmd` | Use Case | 23 use case, 2 aktor |
| `activity-login.mmd` | Activity | Proses login dengan 2FA |
| `activity-transaksi.mmd` | Activity | Proses transaksi POS |
| `activity-laporan.mmd` | Activity | Generate dan export laporan |
| `sequence-login.mmd` | Sequence | Interaksi login (6 objek) |
| `sequence-transaksi.mmd` | Sequence | Interaksi transaksi (6 objek) |
| `sequence-laporan.mmd` | Sequence | Interaksi generate laporan |
| `class-diagram.mmd` | Class | 8 class model + relasi |
| `erd.mmd` | ERD | 10 entitas database |

## Cara Render Diagram (Mermaid.js)

```bash
# VS Code: Install extension "Markdown Preview Mermaid Support"
# Buka file .mmd, preview langsung

# Atau via CLI (npx):
npx @mermaid-js/mermaid-cli docs/bab-iv/diagrams/*.mmd

# Atau via online: https://mermaid.live/
# Paste konten .mmd untuk render dan export PNG/SVG
```

---

## Catatan

- **Format output final:** Copy dari Markdown ke Word/LaTeX dengan penyesuaian template kampus
- **Screenshot:** Ambil dari aplikasi yang running, simpan di `images/`
- **Diagram:** Render Mermaid (.mmd) via editor atau `npx @mermaid-js/mermaid-cli` untuk export PNG
