# Catatan Kurs USD ke IDR

## Pengaturan saat ini:
- **Kurs yang digunakan**: 1 USD = Rp 15.500
- **File yang diubah**: `admin_dashboard.php`

## Cara mengubah kurs:

1. Buka file `admin_dashboard.php`
2. Cari baris berikut (sekitar baris 11-12):
   ```php
   define('USD_TO_IDR_RATE', 15500);
   ```
3. Ubah angka `15500` sesuai dengan kurs terbaru
4. Simpan file

## Contoh:
Jika kurs terbaru adalah 1 USD = Rp 16.000, ubah menjadi:
```php
define('USD_TO_IDR_RATE', 16000);
```

## Bagian yang terpengaruh:
- ✅ Statistik Harian (Hari Ini)
- ✅ Statistik Mingguan (Minggu Ini)
- ✅ Statistik Bulanan (Bulan Ini)
- ✅ Statistik Total
- ✅ Transaksi Terbaru (kolom Total)
- ✅ Item Paling Laris (kolom Pendapatan)
- ✅ Statistik Metode Pembayaran (Total Pendapatan)

## Catatan:
- Harga di database tetap dalam USD
- Konversi hanya dilakukan saat menampilkan di dashboard
- Tidak perlu mengubah data di database
