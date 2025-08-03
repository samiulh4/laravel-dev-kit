# 📦 Laravel 12 Modular Application

This application is built with modern web development tools and best practices using Laravel 12, PHP 8.2, Composer 2.8, and Node.js 20. It also leverages Laravel Reverb for broadcasting and L5 Modular for module-based architecture.

---

## 🛠 Development Stack

- **PHP**: `8.4.*`
- **Composer**: `2.8.*`
- **Node.js**: `20.*`
- **Laravel**: `12.x`

---

## ✅ Laravel 12 Requirements

Make sure your system meets the following requirements before running this application:

### PHP Version
- PHP `≥ 8.2.0`

### Composer Version
- Composer `≥ 2.5.0`

### Required PHP Extensions
- `BCMath`
- `Ctype`
- `Fileinfo`
- `JSON`
- `Mbstring`
- `OpenSSL`
- `PDO`
- `Tokenizer`
- `XML`

---

## ⚡ Broadcasting Setup (Laravel Reverb)

This application uses [Laravel Reverb](https://laravel.com/docs/12.x/reverb) for real-time broadcasting.

### Installation Steps

```bash
php artisan install:broadcasting --reverb
composer require laravel/reverb
php artisan reverb:install
npm install --save-dev laravel-echo pusher-js

## Clear Cache & Optimize
composer dump-autoload
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```
