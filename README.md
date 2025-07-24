# 📦 Warehouse Management System (WMS)

## ✨ Overview

This is a Warehouse Management System (WMS) built with Laravel. It is designed to streamline order handling and inventory processes, with built-in RESTful API integration for courier services. The system includes role-based access control, providing customized dashboards for different warehouse staff.

## 🚀 Features

- Order Management (Create, Track, Update)
- Inventory Control & Product Listings
- Role-Based Dashboard (Admin, Picker, Packer, Manager)
- RESTful API Integration with Courier Services
- Login & Secure Authentication
- Real-Time Status Updates

## 🛠️ Tech Stack

- **Backend**: Laravel 10 (PHP)
- **Frontend**: Blade, Bootstrap
- **Database**: MySQL
- **APIs**: RESTful endpoints for courier integration
- **Tools**: Composer, Laravel Sanctum / Passport (optional for API), Git

## 📦 Installation

```bash
# Clone the repository
git clone https://github.com/your-username/wms.git

# Enter project folder
cd wms

# Install dependencies
composer install

# Copy environment file and configure it
cp .env.example .env

# Generate app key
php artisan key:generate

# Set up your .env with DB and other configs

# Run migrations
php artisan migrate

# (Optional) Seed sample data
php artisan db:seed

# Serve the app
php artisan serve
