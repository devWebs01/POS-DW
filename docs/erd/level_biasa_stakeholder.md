# ERD — Level Biasa (Stakeholder Version)

## Entitas Utama

```
[Kategori] ───1───N─── [Produk]
                           │
                           N
                           │
                    [Item Transaksi] ───N───1─── [Transaksi]
                                                      │
                                                      │1
                                                      │
                                                   [Pengguna]
```

## Penjelasan Data Flow

1. **Kategori** → Mengelompokkan barang dagangan (contoh: Makanan, Minuman, Snack)
2. **Produk** → Data barang yang dijual (nama, harga, stok)
3. **Transaksi** → Satu struk pembelian (nomor invoice, total, pembayaran)
4. **Item Transaksi** → Baris per barang dalam satu transaksi
5. **Pengguna** → Admin yang memproses transaksi

## Alur Bisnis

```
Kategori → Produk → Keranjang (via Item Transaksi) → Transaksi → Laporan
```

## Sederhananya

- **Produk** adalah barang yang dijual, dikelompokkan dalam **Kategori**
- Saat admin menjual barang, tercipta **Transaksi** yang berisi **Item Transaksi** (daftar barang yang dibeli)
- Setiap transaksi dicatat siapa **Pengguna** (admin) yang melayaninya
- Dari data transaksi, pemilik toko bisa melihat **Laporan Penjualan**
