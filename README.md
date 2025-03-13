# Cacha Store - Sistem POS dan Inventori

## Tentang Aplikasi

Cacha Store adalah aplikasi Point of Sale (POS) dan manajemen inventori berbasis web yang dibangun dengan Laravel. Sistem ini dirancang untuk membantu pemilik toko dan kasir dalam mengelola penjualan, inventori, harga, transaksi, dan laporan secara efisien.

## Fitur Utama

### 1. Manajemen Pengguna
- Multi-role (Admin dan Kasir)
- Login dengan "remember me"
- Kontrol akses berbasis peran
- Manajemen sesi pengguna

### 2. Manajemen Produk
- Operasi CRUD untuk produk
- Generasi dan scanning barcode (Code128)
- Konversi multi-unit
- Manajemen kategori
- Pengaturan harga dasar
- Multiple product images dengan primary image
- Landing page support dengan SEO optimization
- Deskripsi produk lengkap dan singkat
- Featured products untuk highlight di landing page

### 3. Manajemen Inventori
- Tracking stok realtime
- Inventori multi-unit
- Peringatan stok menipis
- Pengaturan stok minimum
- Riwayat pergerakan stok
- Indikator status stok

### 4. Manajemen Harga
- Konfigurasi harga dasar
- Sistem harga bertingkat
- Integrasi pajak
- Manajemen diskon

### 5. Point of Sale (POS)
- Interface POS intuitif
- Scanning barcode
- Pencarian produk manual
- Pilihan multi-unit
- Kalkulasi realtime
- Multiple metode pembayaran
- Generasi invoice otomatis
- Cetak struk

### 6. Manajemen Transaksi
- Daftar semua transaksi dengan pagination
- Manajemen transaksi tertunda
- Detail history transaksi
- Status tracking transaksi
- Fitur lanjutkan transaksi

### 7. Stock Opname
- Input stok fisik per unit
- Komparasi stok sistem vs fisik
- Penyesuaian stok otomatis
- Riwayat stock opname
- Pencarian & filter produk
- Dukungan scanner barcode

### 8. Sistem Pelaporan
- Laporan penjualan (harian/bulanan)
- Laporan inventori
- Analisis produk terlaris
- Kalkulasi profit
- Ekspor ke PDF/Excel

### 9. Toko Online
- Landing page produk
- Optimasi SEO
- Management gambar produk
- Showcase produk unggulan

## Teknologi yang Digunakan

- **Framework**: Laravel 11
- **PHP**: 8.2+
- **Database**: MySQL
- **Frontend**: Blade, JavaScript, Bootstrap
- **Dependensi Utama**:
  - artesaos/seotools: Optimasi SEO
  - barryvdh/laravel-dompdf: Generasi PDF
  - maatwebsite/excel: Import/export Excel
  - milon/barcode: Generasi barcode
  - spatie/laravel-sluggable: Pembuatan slug otomatis
  - yajra/laravel-datatables-oracle: Handling datatables

## Instalasi

### Persyaratan Sistem
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL atau MariaDB

### Langkah Instalasi

1. **Clone Repositori**
   ```
   git clone [URL_REPOSITORY]
   cd laravel-cacha-store
   ```

2. **Instal Dependensi PHP**
   ```
   composer install
   ```

3. **Instal Dependensi JavaScript**
   ```
   npm install
   ```

4. **Konfigurasi Lingkungan**
   ```
   cp .env.example .env
   php artisan key:generate
   ```

5. **Konfigurasi Database**
   - Edit file `.env` dan sesuaikan konfigurasi database:
     ```
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=cacha_store
     DB_USERNAME=root
     DB_PASSWORD=
     ```

6. **Migrasi dan Seed Database**
   ```
   php artisan migrate --seed
   ```

7. **Kompilasi Aset**
   ```
   npm run dev
   ```
   atau untuk produksi:
   ```
   npm run build
   ```

8. **Jalankan Aplikasi**
   ```
   php artisan serve
   ```
   Aplikasi akan berjalan di `http://localhost:8000`

## Kredensial Default

- **Admin**
  - Email: admin@example.com
  - Password: password

- **Kasir**
  - Email: kasir@example.com
  - Password: password

## Alur Penggunaan

### Alur POS
1. Login sebagai Kasir atau Admin
2. Buka halaman POS
3. Tambahkan produk ke keranjang (scan barcode atau cari manual)
4. Pilih unit dan jumlah
5. Proses pembayaran
6. Cetak struk

### Alur Stock Opname
1. Mulai sesi stock opname
2. Scan/input produk
3. Catat stok fisik
4. Sistem membandingkan dengan data
5. Review perbedaan
6. Lakukan penyesuaian
7. Simpan riwayat

## Pemeliharaan

### Backup Database
```
php artisan backup:run
```

### Update Aplikasi
```
git pull
composer install
php artisan migrate
npm install
npm run build
php artisan optimize:clear
```

## Dukungan

Jika Anda mengalami masalah atau memiliki pertanyaan, silakan hubungi support di [email_support@example.com]

## Lisensi

Aplikasi ini dilisensikan di bawah [Lisensi MIT](LICENSE).
