# Traffic Reports & Road Safety System - User Report

## Project Overview

Traffic Reports & Road Safety System built with **Laravel 12 + FilamentPHP v3 + Tailwind CSS + Alpine.js**.

---

## System Accounts

### Admin Panel (`/admin`)

| Field | Value |
|-------|-------|
| URL | `http://127.0.0.1:8000/admin` |
| Email | `admin@traffic.com` |
| Password | `password` |

### Police Panel (`/police`)

| Department | Email | Password |
|-----------|-------|----------|
| Highway Patrol | `officer_highway_patrol@traffic.com` | `password` |
| Traffic Police | `officer_traffic_police@traffic.com` | `password` |
| Local Police | `officer_local_police@traffic.com` | `password` |

### Citizen Portal (`/citizen/dashboard`)

Register a new account at `/register` or use one of the seeded citizen accounts. Password for all: `password`.

---

## Seeded Data Summary

| Category | Count |
|----------|-------|
| Roles | 3 (citizen, admin, police) |
| Users | 9 |
| Citizens | 5 |
| Police Officers | 3 |
| Admins | 1 |
| Vehicles | 12 |
| Reports | 50 |
| Traffic Violations | 22 |

---

## Steps to Run the Project

### 1. Prerequisites

- PHP >= 8.2
- MySQL
- Composer
- Node.js & npm

### 2. Clone & Install

```bash
composer install
npm install
```

### 3. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your database credentials:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=traffic_app
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Create Database

Create a MySQL database named `traffic_app`:

```sql
CREATE DATABASE traffic_app;
```

### 5. Run Migrations & Seed

```bash
php artisan migrate:fresh --seed
```

### 6. Build Frontend Assets

```bash
npm run build
```

### 7. Start the Server

```bash
php artisan serve
```

The application will be available at: `http://127.0.0.1:8000`

---

## Application URLs

| Page | URL |
|------|-----|
| Home | `http://127.0.0.1:8000` |
| Login | `http://127.0.0.1:8000/login` |
| Register | `http://127.0.0.1:8000/register` |
| Admin Panel | `http://127.0.0.1:8000/admin` |
| Police Panel | `http://127.0.0.1:8000/police` |
| Citizen Dashboard | `http://127.0.0.1:8000/citizen/dashboard` |
| New Report Wizard | `http://127.0.0.1:8000/citizen/reports/create` |

---

## Key Features

- **Admin Panel**: Full CRUD for users, read-only view for vehicles/reports/activity logs + stats widgets & chart
- **Police Panel**: Officers see only reports assigned to their department, can update report status
- **Citizen Portal**: Register, manage vehicles, submit reports via multi-step wizard with GPS auto-detection
- **Smart Auto-Routing**: Reports are automatically routed to the correct department based on type and location
- **Bilingual**: Arabic/English with RTL support
- **Dark Mode**: Toggle on all pages
