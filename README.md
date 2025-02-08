# Toko Cacha POS System Documentation

A comprehensive Point of Sale (POS) system for minimarket management built with Laravel 11.

## Table of Contents
- [Features](#features)
- [System Requirements](#system-requirements)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
- [Core Modules](#core-modules)
- [Database Structure](#database-structure)
- [Usage Guide](#usage-guide)

## Features

### 1. User Management
- Multi-role authentication (Admin, Cashier)
- Login with remember me functionality
- Role-based access control
- User session management

### 2. Product Management
- CRUD operations for products
- Barcode generation and scanning
  - Supports Code128 format
  - Auto-generate or upload barcodes
  - Barcode image storage
- Multiple unit conversion
  - Base unit and conversion units
  - Price calculation per unit
- Category management
- Base price settings

### 3. Inventory Management
- Real-time stock tracking
- Multi-unit inventory
- Low stock alerts
- Minimum stock settings
- Stock movement history
- Stock status indicators (Safe/Low)

### 4. Price Management
- Base price configuration
- Tiered pricing system
  - Quantity-based pricing
  - Unit-based pricing
- Tax integration
- Discount management
  - Percentage-based discounts
  - Fixed amount discounts

### 5. Point of Sale (POS)
- Intuitive POS interface
- Barcode scanning
- Manual product search
- Multi-unit selection
- Real-time calculations
- Multiple payment methods
  - Cash
  - Transfer
- Automatic invoice generation
- Receipt printing

### 6. Reporting System
- Sales reports
  - Daily
  - Monthly
- Inventory reports
- Best-seller analysis
- Profit calculations
- Export functionality
  - PDF format
  - Excel format

## System Requirements
- PHP >= 8.1
- Laravel 11
- MySQL/PostgreSQL
- Modern web browser
- Composer
- Node.js & NPM
- Web server (Apache/Nginx)

## Tech Stack
- **Backend:** Laravel 11
- **Frontend:**
  - Bootstrap 5
  - JavaScript
  - jQuery
- **Database:** MySQL/PostgreSQL
- **Additional Libraries:**
  - DNS1D (Barcode generation)
  - DomPDF (PDF generation)
  - Laravel Excel

## Installation

1. Clone the repository
```bash
git clone [repository-url]
```

2. Install dependencies
```bash
composer install
npm install
```

3. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Setup database
```bash
php artisan migrate
php artisan db:seed
```

5. Start the application
```bash
php artisan serve
npm run dev
```

## Core Modules

### 1. Authentication Module
- Login/Logout functionality
- Remember me feature
- Password management
- Session handling

### 2. Product Module
- Product information management
- Barcode handling
- Unit conversion system
- Price management
- Category organization

### 3. Inventory Module
- Stock management
- Alert system
- Stock movement tracking
- Multi-unit inventory handling

### 4. Transaction Module
- POS interface
- Payment processing
- Invoice generation
- Transaction history

### 5. Reporting Module
- Sales analysis
- Stock reports
- Revenue tracking
- Export functionality

## Database Structure

### Key Tables
1. **users**
   - id, name, email, password, role
   - Remember token for persistent login

2. **products**
   - Basic product information
   - Barcode data
   - Base price
   - Category reference

3. **product_units**
   - Unit conversion settings
   - Price per unit
   - Default unit flags

4. **inventories**
   - Current stock levels
   - Minimum stock settings
   - Unit references

5. **transactions**
   - Sales records
   - Payment information
   - Customer references
   - Total calculations

6. **price_tiers**
   - Quantity-based pricing
   - Unit-specific pricing
   - Minimum quantity thresholds

## Usage Guide

### POS Operation
1. Login to the system
2. Access POS interface
3. Add products via:
   - Barcode scanning
   - Manual search
4. Select appropriate units
5. Adjust quantities
6. Process payment
7. Print receipt

### Inventory Management
1. Regular stock monitoring
2. Set minimum stock levels
3. Respond to low stock alerts
4. Update stock quantities
5. Track stock movements

### Reporting
1. Generate daily/monthly reports
2. Export in desired format
3. Analyze sales patterns
4. Monitor profit margins
5. Track best-selling items

## Security Considerations
- CSRF protection
- Input validation
- User authentication
- Role-based access
- Secure password handling
- Session security

## Maintenance
- Regular database backups
- Log monitoring
- Security updates
- Performance optimization
- Stock level reviews

## Support
For technical support or questions, please contact the system administrator.





<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
