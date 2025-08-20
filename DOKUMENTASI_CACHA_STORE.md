# DOKUMENTASI LENGKAP CACHA STORE
## Sistem Point of Sale dan Manajemen Inventori Berbasis Web

---

## DAFTAR ISI
1. [Pendahuluan](#pendahuluan)
2. [Arsitektur Sistem](#arsitektur-sistem)
3. [Fitur Utama](#fitur-utama)
4. [Struktur Database](#struktur-database)
5. [Modul dan Komponen](#modul-dan-komponen)
6. [Alur Kerja](#alur-kerja)
7. [API dan Endpoint](#api-dan-endpoint)
8. [Keamanan](#keamanan)
9. [Instalasi dan Konfigurasi](#instalasi-dan-konfigurasi)
10. [Pemeliharaan](#pemeliharaan)
11. [Troubleshooting](#troubleshooting)

---

## PENDAHULUAN

### Deskripsi Aplikasi
Cacha Store adalah sistem Point of Sale (POS) dan manajemen inventori yang komprehensif, dirancang untuk memenuhi kebutuhan bisnis retail modern. Aplikasi ini menggabungkan fungsionalitas POS tradisional dengan fitur e-commerce dan manajemen inventori yang canggih.

### Tujuan Pengembangan
- Menyediakan solusi POS yang user-friendly dan efisien
- Mengoptimalkan manajemen inventori dengan tracking real-time
- Memberikan insight bisnis melalui laporan yang detail
- Mendukung operasi multi-store dengan manajemen terpusat
- Menyediakan platform e-commerce untuk penjualan online

### Target Pengguna
- **Admin**: Manajemen sistem, laporan, dan konfigurasi
- **Kasir**: Operasi POS dan transaksi harian
- **Customer**: Pembelian online melalui landing page
- **Manager**: Monitoring performa dan analisis bisnis

---

## ARSITEKTUR SISTEM

### Teknologi Stack
- **Backend**: Laravel 11 (PHP 8.2+)
- **Database**: MySQL/MariaDB
- **Frontend**: Blade Templates, Bootstrap, JavaScript
- **Server**: Apache/Nginx
- **Queue**: Laravel Queue System

### Dependensi Utama
```json
{
  "artesaos/seotools": "^1.3",           // Optimasi SEO
  "barryvdh/laravel-dompdf": "^3.1",     // Generasi PDF
  "maatwebsite/excel": "^3.1",           // Import/Export Excel
  "milon/barcode": "^11.0",              // Generasi Barcode
  "spatie/laravel-sluggable": "^3.7",    // Pembuatan Slug
  "yajra/laravel-datatables-oracle": "^11.1" // DataTables
}
```

### Struktur Direktori
```
laravel-cacha-store/
├── app/
│   ├── Models/           # Model Eloquent
│   ├── Http/Controllers/ # Controller
│   ├── Services/         # Business Logic
│   ├── Repositories/     # Data Access Layer
│   ├── Helpers/          # Helper Functions
│   ├── Enums/           # Enumeration Classes
│   ├── Jobs/            # Queue Jobs
│   ├── Mail/            # Email Templates
│   ├── Imports/         # Excel Import Classes
│   ├── Exports/         # Excel Export Classes
│   └── Traits/          # Reusable Traits
├── resources/views/     # Blade Templates
├── routes/              # Route Definitions
├── database/            # Migrations & Seeders
├── public/              # Public Assets
└── config/              # Configuration Files
```

---

## FITUR UTAMA

### 1. Manajemen Pengguna dan Autentikasi
**Fitur:**
- Multi-role system (Admin, User/Kasir)
- Login dengan "Remember Me"
- Role-based access control
- Session management
- Password reset functionality

**Controller:** `AuthController`, `UserController`
**Model:** `User`, `Group`

### 2. Manajemen Produk
**Fitur:**
- CRUD operasi produk lengkap
- Multi-unit support (pcs, kg, liter, dll)
- Barcode generation (Code128)
- Multiple product images
- SEO optimization
- Featured products
- Product categories
- Import/Export Excel

**Controller:** `ProductController`, `ProductImportController`
**Model:** `Product`, `ProductUnit`, `ProductImage`, `Category`

### 3. Point of Sale (POS)
**Fitur:**
- Interface POS yang intuitif
- Barcode scanning
- Manual product search
- Multi-unit selection
- Real-time calculation
- Multiple payment methods
- Automatic invoice generation
- Receipt printing
- Pending transaction management

**Controller:** `POSController`
**Model:** `Transaction`, `TransactionItem`

### 4. Manajemen Inventori
**Fitur:**
- Real-time stock tracking
- Multi-unit inventory
- Low stock alerts
- Stock minimum settings
- Stock movement history
- Stock status indicators

**Controller:** `StockHistoryController`, `StockAdjustmentController`
**Model:** `StockHistory`, `StockAdjustment`

### 5. Stock Opname
**Fitur:**
- Physical stock input per unit
- System vs physical comparison
- Automatic stock adjustment
- Stock opname history
- Product search & filter
- Barcode scanner support

**Controller:** `StockTakeController`
**Model:** `StockTake`, `StockTakeItem`

### 6. Manajemen Harga
**Fitur:**
- Base price configuration
- Tiered pricing system
- Tax integration
- Discount management
- Price history tracking

**Controller:** `ProductPriceController`, `TaxController`, `DiscountController`
**Model:** `Price`, `Tax`, `Discount`

### 7. Purchase Order
**Fitur:**
- Purchase order creation
- Supplier management
- Order tracking
- Receipt management
- Cost analysis

**Controller:** `PurchaseOrderController`
**Model:** `PurchaseOrder`, `PurchaseOrderItem`, `Supplier`

### 8. Customer Management
**Fitur:**
- Customer database
- Customer history
- Customer analytics
- Contact management

**Controller:** `CustomerController`
**Model:** `Customer`

### 9. Store Management
**Fitur:**
- Multi-store support
- Store balance management
- Store performance tracking
- Store configuration

**Controller:** `StoreController`, `StoreBalanceController`
**Model:** `Store`, `StoreBalance`, `BalanceMutation`

### 10. Reporting System
**Fitur:**
- Sales reports (daily/monthly)
- Inventory reports
- Financial reports
- Customer reports
- Store performance reports
- Chart visualizations
- PDF/Excel export

**Controller:** `ReportController`
**Views:** `resources/views/reports/`

### 11. E-commerce Features
**Fitur:**
- Product landing page
- SEO optimization
- Product showcase
- Contact form
- Guest access

**Controller:** `GuestController`
**Views:** `resources/views/guest/`

---

## STRUKTUR DATABASE

### Tabel Utama

#### 1. users
- Primary user authentication
- Role-based access control
- Store association

#### 2. stores
- Multi-store configuration
- Store settings and status

#### 3. products
- Product master data
- SEO fields (slug, meta)
- Featured product flag

#### 4. product_units
- Multi-unit configuration
- Conversion rates
- Stock levels per unit

#### 5. product_images
- Multiple product images
- Primary image designation
- Image ordering

#### 6. categories
- Product categorization
- Hierarchical structure

#### 7. transactions
- Sales transactions
- Payment information
- Transaction status

#### 8. transaction_items
- Transaction line items
- Quantity and pricing

#### 9. stock_histories
- Stock movement tracking
- Audit trail

#### 10. stock_takes
- Stock opname sessions
- Physical count data

#### 11. purchase_orders
- Purchase order management
- Supplier relationships

#### 12. store_balances
- Store financial tracking
- Balance mutations

### Relasi Database
```
users -> stores (belongsTo)
products -> categories (belongsTo)
products -> product_units (hasMany)
products -> product_images (hasMany)
transactions -> transaction_items (hasMany)
transactions -> users (belongsTo)
stock_histories -> products (belongsTo)
purchase_orders -> suppliers (belongsTo)
```

---

## MODUL DAN KOMPONEN

### 1. Authentication Module
**File:** `app/Http/Controllers/AuthController.php`
**Middleware:** `auth`, `guest`
**Routes:** `/login`, `/logout`

### 2. Dashboard Module
**File:** `app/Http/Controllers/DashboardController.php`
**Features:** Analytics, charts, summary data
**Routes:** `/dashboard`

### 3. Product Management Module
**File:** `app/Http/Controllers/ProductController.php`
**Features:** CRUD, images, import/export
**Routes:** `/products/*`

### 4. POS Module
**File:** `app/Http/Controllers/POSController.php`
**Features:** Sales interface, barcode scanning
**Routes:** `/pos/*`

### 5. Transaction Module
**File:** `app/Http/Controllers/TransactionController.php`
**Features:** Transaction management, returns
**Routes:** `/transactions/*`

### 6. Inventory Module
**File:** `app/Http/Controllers/StockHistoryController.php`
**Features:** Stock tracking, adjustments
**Routes:** `/stock/*`

### 7. Reporting Module
**File:** `app/Http/Controllers/ReportController.php`
**Features:** Various business reports
**Routes:** `/reports/*`

### 8. E-commerce Module
**File:** `app/Http/Controllers/GuestController.php`
**Features:** Public product display
**Routes:** `/`, `/shop/*`

---

## ALUR KERJA

### Alur POS (Point of Sale)
1. **Login** - Kasir login ke sistem
2. **Buka POS** - Akses interface POS
3. **Scan/Cari Produk** - Input produk via barcode atau pencarian
4. **Pilih Unit** - Tentukan unit dan quantity
5. **Review Cart** - Periksa item di keranjang
6. **Proses Pembayaran** - Pilih metode pembayaran
7. **Cetak Struk** - Generate dan print receipt
8. **Selesai** - Transaksi complete

### Alur Stock Opname
1. **Mulai Sesi** - Buat stock opname baru
2. **Input Produk** - Scan atau pilih produk
3. **Catat Stok Fisik** - Input jumlah fisik
4. **Bandingkan** - Sistem compare dengan data
5. **Review Selisih** - Periksa perbedaan
6. **Penyesuaian** - Lakukan adjustment jika perlu
7. **Simpan** - Complete stock opname

### Alur Purchase Order
1. **Buat PO** - Create purchase order
2. **Pilih Supplier** - Assign supplier
3. **Tambah Item** - Add products to order
4. **Review** - Check order details
5. **Submit** - Submit for approval
6. **Receive** - Receive goods
7. **Update Stock** - Update inventory

---

## API DAN ENDPOINT

### Public Routes
```
GET  /                    - Landing page
GET  /shop               - Product catalog
GET  /shop/{slug}        - Product detail
GET  /login              - Login form
POST /login              - Login process
```

### Authenticated Routes
```
GET  /dashboard          - Dashboard
GET  /pos                - POS interface
POST /pos                - Create transaction
GET  /transactions       - Transaction list
GET  /products           - Product management
GET  /reports            - Report dashboard
```

### AJAX Endpoints
```
GET  /pos/get-product    - Get product by barcode
GET  /pos/search-product - Search products
GET  /dashboard/chart-data - Dashboard charts
GET  /reports/sales/payment-chart - Payment chart
```

---

## KEAMANAN

### Authentication
- Laravel's built-in authentication
- Session-based security
- CSRF protection
- Password hashing

### Authorization
- Role-based access control
- Middleware protection
- Route-level permissions

### Data Protection
- Input validation
- SQL injection prevention
- XSS protection
- File upload security

### Best Practices
- Environment configuration
- Secure headers
- HTTPS enforcement
- Regular security updates

---

## INSTALASI DAN KONFIGURASI

### Persyaratan Sistem
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL/MariaDB
- Web server (Apache/Nginx)

### Langkah Instalasi

#### 1. Clone Repository
```bash
git clone [repository-url]
cd laravel-cacha-store
```

#### 2. Install Dependencies
```bash
composer install
npm install
```

#### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

#### 4. Database Configuration
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cacha_store
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### 5. Database Migration
```bash
php artisan migrate --seed
```

#### 6. Asset Compilation
```bash
npm run dev
# atau untuk production
npm run build
```

#### 7. Storage Setup
```bash
php artisan storage:link
```

#### 8. Queue Setup (Optional)
```bash
php artisan queue:work
```

### Konfigurasi Tambahan

#### Printer Settings
- Akses: `/settings/printer`
- Konfigurasi printer thermal
- Test print functionality

#### SEO Configuration
- Meta tags setup
- Sitemap generation
- Social media tags

#### Email Configuration
- SMTP settings
- Email templates
- Notification setup

---

## PEMELIHARAAN

### Backup Strategy
```bash
# Database backup
php artisan backup:run

# File backup
tar -czf backup-$(date +%Y%m%d).tar.gz storage/ public/
```

### Update Process
```bash
git pull origin main
composer install
php artisan migrate
npm install
npm run build
php artisan optimize:clear
```

### Monitoring
- Error logging
- Performance monitoring
- Database optimization
- Cache management

### Maintenance Tasks
```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Optimize application
php artisan optimize

# Check application status
php artisan about
```

---

## TROUBLESHOOTING

### Common Issues

#### 1. Database Connection
**Problem:** Cannot connect to database
**Solution:** Check `.env` configuration and database server status

#### 2. Permission Issues
**Problem:** Storage or cache directory not writable
**Solution:** Set proper permissions (755 for directories, 644 for files)

#### 3. Asset Loading
**Problem:** CSS/JS not loading
**Solution:** Run `npm run build` and check `public/build` directory

#### 4. Queue Issues
**Problem:** Jobs not processing
**Solution:** Start queue worker with `php artisan queue:work`

#### 5. Barcode Issues
**Problem:** Barcode not scanning
**Solution:** Check barcode format and scanner configuration

### Log Files
- Application logs: `storage/logs/laravel.log` daily
- Error logs: Check web server error logs
- Queue logs: `storage/logs/queue.log`

### Debug Mode
```env
APP_DEBUG=true
APP_ENV=local
```

---

## KESIMPULAN

Cacha Store adalah sistem POS dan inventori yang komprehensif dengan fitur-fitur modern untuk mendukung operasi bisnis retail. Sistem ini dirancang dengan arsitektur yang scalable, maintainable, dan user-friendly.

### Keunggulan Sistem
- **Comprehensive**: Mencakup semua aspek operasi retail
- **User-friendly**: Interface yang intuitif dan mudah digunakan
- **Scalable**: Mendukung multi-store dan pertumbuhan bisnis
- **Secure**: Implementasi keamanan yang robust
- **Flexible**: Konfigurasi yang dapat disesuaikan

### Roadmap Pengembangan
- Mobile application
- Advanced analytics
- Integration dengan marketplace
- AI-powered inventory prediction
- Advanced reporting features

---

**Dokumentasi ini dibuat untuk versi Cacha Store terbaru dan akan diperbarui sesuai dengan perkembangan sistem.**
