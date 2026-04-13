# WebToko - Photo Studio Payment & Order Management System

A Laravel-based web application for managing orders, payments, and financial reports for a photo studio business.

## Features

### 🎯 Core Functionality
- **Order Management** - Create and track customer orders
- **Role-Based Access Control** - Penerima (Receiver), Kasir (Cashier), Operator Cetak (Print Operator), Admin
- **Payment System** - Support for multiple payment types:
  - Full Payment (Pembayaran Penuh)
  - Down Payment / DP (Uang Muka)
  - Remaining Payment / Pelunasan (Sisa Pembayaran)
- **Financial Reporting** - Cash flow reports based on actual payment dates
- **Order Status Tracking** - Track orders from creation to pickup

### 💳 Payment Methods
- Cash (Tunai)
- Bank Transfer (Transfer)
- QRIS

---

## 📋 System Requirements

- **PHP:** 8.2+
- **Laravel:** 12.56.0+
- **Database:** MySQL 8.0+
- **Node.js:** 18+ (for frontend build tools)

---

## 🚀 Installation

### 1. Clone Repository
```bash
git clone <repository-url>
cd webToko
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database
Update `.env` with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=webtoko
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migrations
```bash
php artisan migrate
```

### 6. Build Frontend Assets
```bash
npm run dev
# or for production
npm run build
```

### 7. Start Development Server
```bash
php artisan serve
```

Access the application at `http://localhost:8000`

---

## 📁 Project Structure

```
webToko/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Request handlers
│   │   └── Middleware/        # Custom middleware
│   ├── Models/                # Eloquent models
│   ├── Services/              # Business logic services
│   └── Providers/
├── database/
│   ├── migrations/            # Database schema
│   ├── factories/             # Model factories
│   └── seeders/               # Database seeders
├── resources/
│   ├── views/                 # Blade templates
│   │   ├── auth/              # Authentication views
│   │   ├── kasir/             # Cashier interface
│   │   ├── operator/          # Operator interface
│   │   ├── penerima/          # Receiver interface
│   │   ├── admin/             # Admin dashboard
│   │   └── layouts/           # Layout components
│   ├── css/
│   └── js/
├── routes/
│   └── web.php                # Web routes
├── config/                    # Configuration files
├── storage/
├── tests/
└── public/                    # Public assets
```

---

## 👥 User Roles

### 1. **Penerima (Receiver)**
- Create new orders from customers
- View own orders

### 2. **Kasir (Cashier)**
- View unpaid orders
- Record payments (Full/DP/Pelunasan)
- View payment details
- Generate financial reports

### 3. **Operator Cetak (Print Operator)**
- View orders ready to print
- Mark orders as printed

### 4. **Admin**
- Full access to all features
- Manage products
- Generate comprehensive financial reports
- View order status and history

---

## 💰 Payment System

### DP (Down Payment) Workflow
1. Customer places order
2. Kasir records DP payment (e.g., Rp 100,000 from Rp 500,000 total)
3. Order status changes to "partial" (partial_dp)
4. Later, kasir records pelunasan payment for remaining balance
5. Order status changes to "paid" when fully settled

### Financial Reporting
- Reports show actual cash flow based on payment dates, not order dates
- Supports filtering by date range
- Breakdown by payment type and method

---

## 🔄 Key Models

### Order
- `id` - Primary key
- `order_code` - Unique order identifier
- `customer_name` - Customer name
- `customer_phone` - Contact number
- `total_price` - Order total
- `dp_amount` - Down payment recorded
- `dp_status` - DP status (no_dp, partial_dp, full_dp)
- `payment_status` - Payment status (unpaid, partial, paid)
- `print_status` - Print status
- `created_at` - Order creation date

### Payment
- `id` - Primary key
- `order_id` - Related order
- `amount` - Payment amount
- `payment_method` - Cash/Transfer/QRIS
- `payment_type` - full/dp/pelunasan
- `payment_date` - When payment was received
- `paid_by` - User who recorded payment

---

## 📊 Financial Reports Available

1. **Daily Report** - Daily cash flow summary with transaction details
2. **Payment Method Report** - Breakdown by payment method
3. **Outstanding DP Report** - Orders with incomplete DP
4. **DP vs Pelunasan Report** - Comparison of down payments vs remaining payments

---

## 🛠 Technologies Used

- **Backend:** Laravel 12, PHP 8.2
- **Frontend:** Blade Templates, Tailwind CSS, Vanilla JavaScript
- **Database:** MySQL 8.0
- **Build Tools:** Vite, npm
- **Icons:** Font Awesome 6
- **Authentication:** Laravel built-in auth

---

## 📝 API Endpoints (Selected)

### Payment Routes
- `POST /kasir/payment/dp` - Record DP payment
- `POST /kasir/payment/pelunasan` - Record remaining payment
- `GET /kasir/payment/{orderId}/detail` - Get payment details

### Report Routes
- `GET /kasir/reports/daily` - Daily financial report
- `GET /kasir/reports/payment-method` - Payment method breakdown
- `GET /kasir/reports/outstanding-dp` - Outstanding DP orders
- `GET /kasir/reports/dp-vs-pelunasan` - DP comparison

---

## 🔐 Security

- CSRF protection enabled
- Role-based access control (middleware)
- User authentication required
- Database query protection via Eloquent ORM
- Sensitive data in `.env` file

---

## 📞 Support

For issues or questions, please contact the development team.

---

## 📄 License

This project is proprietary and owned by Bukit Foto Studio Cab Km9.
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

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
