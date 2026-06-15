# BAB IV - PERANCANGAN DAN IMPLEMENTASI SISTEM

Dokumentasi ini adalah **pendukung** untuk file-file utama Bab IV yang berada di direktori `docs/`. File ini berisi informasi tentang assets (diagram, tabel, gambar).

---

## Struktur Folder

```
docs/
├── BAB-IV-PERANCANGAN-DAN-IMPLEMENTASI.md  ← Indeks utama
├── 4.1-analisis-kebutuhan-sistem.md        ← Sub-bab 4.1
├── 4.2-perancangan-sistem.md               ← Sub-bab 4.2
├── 4.3-perancangan-basis-data.md           ← Sub-bab 4.3
├── 4.4-perancangan-antarmuka.md            ← Sub-bab 4.4
├── 4.5-implementasi-sistem.md              ← Sub-bab 4.5
├── 4.6-pengujian-sistem.md                 ← Sub-bab 4.6
└── bab-iv/
    ├── diagrams/           ← Diagram PlantUML (10 files)
    ├── images/             ← Screenshot aplikasi
    └── tables/             ← Tabel-tabel pendukung
```

## File Utama (di `docs/`)

| File | Status | Deskripsi |
|------|--------|-----------|
| `BAB-IV-PERANCANGAN-DAN-IMPLEMENTASI.md` | ✅ Selesai | Indeks utama dan daftar isi |
| `4.1-analisis-kebutuhan-sistem.md` | ✅ Selesai | Kebutuhan fungsional (35), non-fungsional (30), HW/SW |
| `4.2-perancangan-sistem.md` | ✅ Selesai | Flowchart, Use Case, Activity, Sequence, Class Diagram |
| `4.3-perancangan-basis-data.md` | ✅ Selesai | ERD dan Struktur Tabel (13 tabel) |
| `4.4-perancangan-antarmuka.md` | ✅ Selesai | Mockup 7 halaman utama |
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
