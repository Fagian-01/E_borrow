# E-Borrow - Enterprise Inventory System

<div align="center">
  <img src="public/docs/landing-page.png" alt="E-Borrow Landing Page" width="100%">
  <p><em>Premium Vue 3 & Laravel 12 Inventory Management Experience</em></p>
</div>

E-Borrow is a modern, enterprise-grade inventory and asset loan management system built using the **VILT Stack** (Vue 3, Inertia.js, Laravel 12, Tailwind CSS). It features a sleek, responsive design with dynamic dark mode, robust Role-Based Access Control (RBAC), and automated auditing.

## 🌟 Key Features

* **📦 Catalog & Cart System**: E-commerce style interface for users to browse assets, add to cart, and checkout borrowing requests.
* **🔐 Robust RBAC & Multi-role Management**: Segregated access for Super Admin (User Management), Admin/Staff (Inventory & Approvals), and Users.
* **📈 Rich Dashboard & Analytics**: Interactive charts for borrowing trends, most borrowed items, status distribution, and recent fines.
* **📜 Comprehensive Activity Logging**: Detailed audit trails for every transaction, update, and movement to maintain item accountability.
* **🌑 Luxury Dark Mode Theme**: First-class dark mode support with glassmorphism components, gradients, and custom micro-animations.
* **⚡ Pessimistic Locking**: Safe concurrent transaction handling during checkout using database-level locking.

---

## 📸 Application Gallery

### 🌍 Authentication & Landing
<details>
  <summary>Click to view Authentication interfaces</summary>
  <br>
  <img src="public/docs/landing-page.png" alt="Landing Page" width="100%">
  <br><br>
  <p align="center">
    <img src="public/docs/login.png" alt="Login" width="49%">
    <img src="public/docs/register.png" alt="Register" width="49%">
  </p>
</details>

### 👤 User Interface
<details>
  <summary>Click to view User Portal (Catalog, Cart, and Borrowings)</summary>
  <br>
  <img src="public/docs/user-dashboard.png" alt="User Dashboard" width="100%">
  <br><br>
  <p align="center">
    <img src="public/docs/user-katalog.png" alt="User Catalog" width="49%">
    <img src="public/docs/user-cart.png" alt="User Cart" width="49%">
  </p>
  <img src="public/docs/user-peminjaman.png" alt="User Borrowing History" width="100%">
</details>

### 🛡️ Admin & Staff Dashboard
<details>
  <summary>Click to view Admin Core Dashboard</summary>
  <br>
  <img src="public/docs/admin-dashboard.png" alt="Admin Dashboard" width="100%">
  <br><br>
  <p align="center">
    <img src="public/docs/admin-kategori.png" alt="Category Management" width="49%">
    <img src="public/docs/admin-inventory.png" alt="Inventory Management" width="49%">
  </p>
</details>

### 📦 Borrowing & Approval Workflow
<details>
  <summary>Click to view Request, Approval, and Return Workflow</summary>
  <br>
  <img src="public/docs/admin-peminjaman.png" alt="Borrowing Requests List" width="100%">
  <br><br>
  <p align="center">
    <img src="public/docs/admin-borrow-detail.png" alt="Borrow Request Detail" width="49%">
    <img src="public/docs/admin-persetujuan-toggle.png" alt="Approval Modal Dialog" width="49%">
    <img src="public/docs/admin-penyerahan-barang.png" alt="Asset Handover" width="49%">
    <img src="public/docs/admin-pengembalian.png" alt="Asset Return" width="49%">
  </p>
  <img src="public/docs/admin-borrowings-aproved.png" alt="Approved Status State" width="100%">
</details>

### 📈 Reports & Audit Trail
<details>
  <summary>Click to view Reports and Activity Logs</summary>
  <br>
  <img src="public/docs/admin-report.png" alt="Analytics Dashboard" width="100%">
  <br><br>
  <img src="public/docs/admin-log.png" alt="System Activity Logs" width="100%">
</details>

### 👥 User Management (Super Admin)
<details>
  <summary>Click to view RBAC and User Controls</summary>
  <br>
  <img src="public/docs/admin-users.png" alt="User Management List" width="100%">
  <br><br>
  <img src="public/docs/admin-add-users.png" alt="Create New Staff/User" width="100%">
</details>

---

## 🛠 Tech Stack

* **Backend**: Laravel 12, PHP 8.2+
* **Database**: MySQL
* **Frontend**: Vue 3 (Composition API), Inertia.js, Tailwind CSS v3
* **Tooling**: Vite, Composer, NPM

## 🚀 Installation & Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd e-borrow
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Install NPM Dependencies**
   ```bash
   npm install
   ```

4. **Environment Setup**
   Copy the example environment file and configure your database settings.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Make sure your database connection (e.g., MySQL) is correctly set up in `.env`.*

5. **Migrate and Seed the Database**
   This command will run all migrations and seed dummy data, including the admin and user accounts.
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Storage Link**
   Publish the local storage to public so images can be accessed.
   ```bash
   php artisan storage:link
   ```

7. **Run the Development Servers**
   Open two terminal windows:
   ```bash
   # Terminal 1: Run the Laravel server
   php artisan serve
   
   # Terminal 2: Run the Vite development server
   npm run dev
   ```

## 👥 Default Accounts

Users are generated via Database Seeder. 
Default Password for all seeded accounts is: **`password`**

* **Super Admin**: `admin@eborrow.test`
* **Staff**: `staff@eborrow.test`
* **User**: `user@eborrow.test`

## 📁 Project Structure

* `app/Http/Controllers/`
  * `Admin/` - Logic for administrators (Approvals, Item Management, Logs, Reports)
  * `User/` - Logic for regular users (Catalog, Cart, User Borrowing status)
* `resources/js/`
  * `Pages/Admin/` - Vue pages for the admin dashboard
  * `Pages/User/` - Vue pages for catalog and user portals
  * `Layouts/` - Shared authenticated and guest layouts
  * `Components/` - Reusable UI widgets and elements

## 🔒 Security Measures
- Route protection using granular Inertia middleware.
- Database locking `lockForUpdate()` preventing over-borrowing race conditions.
- Strict Eloquent type-casting and authorization policies.

---
*Developed with ♥️ for smarter asset management.*
