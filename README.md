# Laravel Medical Record Management System

A full-featured backend API for managing medical records in a hospital setting. Built with Laravel 12 and designed for scalability, security, and real-world implementation. Ideal for clinics, hospitals, or any healthcare provider needing digital record management.

## Features

- Token based Authentication (Login, Register)
- Role-based access control (`ADMIN`, `DOCTER`, `NURSE`)
- CRUD:
  - Users
  - Docters (Specialization)
  - Patients
  - Medical Records
  - Appointment
- Relational data structure 
- Search & filtering endpoints
- Full test coverage with Pest
- RESTful JSON response standard
- API documentation (Swagger Ready)

## Technologies Used

- Laravel 12
- MySQL
- Laravel Sanctum 
- Pest for testing
- Eloquent ORM
- Swagger for API documentation

## Getting Started

### Requirements

- PHP >= 8.2
- Composer
- MySQL or MariaDB
- Laravel CLI

### Installation & Set Up Environtment

install project and dependencies
```bash
git clone https://github.com/tado3002/medicalRecordSystem-laravel.git
cd medicalRecordSystem-laravel
composer install
cp .env.example .env
```

setup database environtment
> Lalu ubah konfigurasi database di .env sesuai dengan lokalmu:
```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hospital_system
DB_USERNAME=root
DB_PASSWORD=
```
seed database and running the project
```bash
php artisan migrate --seed
php artisan serve
```

## API Documentation

Dokumentasi API lengkap dapat dilihat dengan membuka file di folder `docs/` menggunakan **Swagger Extension** di VSCode.

> 📂 Buka salah satu file di `docs/users-spec-api.yaml` dengan ekstensi "Swagger Viewer" atau "Swagger Preview" untuk melihat dokumentasi interaktifnya.

## Testing

```bash
php artisan test
```

## Authentication
Gunakan endpoint /api/auth/login untuk mendapatkan token . Sertakan token pada header Authorization:

```css
Authorization: Bearer {token}
```

## Demo Account

| Role   | Email                                           | Password     |
| ------ | ----------------------------------------------- | -------------|
| Admin  | [test@gmail.com]                                | testtesttest |

