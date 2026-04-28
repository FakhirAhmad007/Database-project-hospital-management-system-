# Hospital Management System

A browser-based hospital management web app built with vanilla HTML/CSS/JS
and a PHP + MySQL backend.

## Features
- In-patient & out-patient management (full CRUD)
- Doctor & staff directory
- Room occupancy tracking
- Billing overview with print/PDF export
- Server-side session authentication
- XSS-safe rendering throughout
- SQL-injection-safe prepared statements on all queries

---

## Setup

### 1. Database
```bash
mysql -u root -p < schema.sql
```
This creates the `hospital_db` database and all tables.
Edit `schema.sql` first if you need a different DB name.

### 2. Environment file
Copy `.env.example` to `.env` and fill in your values:
```
DB_HOST=localhost
DB_USER=your_db_username
DB_PASS=your_db_password
DB_NAME=hospital_db

ADMIN_USER=admin
ADMIN_HASH=<bcrypt hash — see below>
```

To generate a bcrypt hash for your chosen password run:
```bash
php generate_hash.php
```
Paste the output into `.env` as `ADMIN_HASH=...`

The default credentials (for development only) are:
- Username: `admin`
- Password: `admin123`

**Change these before any production deployment.**

### 3. Web server
Place the project folder inside your XAMPP `htdocs` directory, then open:
```
http://localhost/hospital_fixed/src/pages/index.html
```

Or deploy to any PHP 7.4+ / MySQL 5.7+ host.

---

## Project structure
```
hospital_fixed/
├── .env                  ← credentials (gitignored)
├── .env.example          ← template to share safely
├── .gitignore
├── schema.sql            ← full database schema + seed data
├── generate_hash.php     ← one-time CLI tool to hash a password
├── README.md
└── src/
    ├── assets/css/style.css
    ├── pages/            ← all HTML views
    │   ├── index.html    ← login
    │   ├── dashboard.html
    │   ├── inpatients.html
    │   ├── outpatients.html
    │   ├── add_inpatient.html
    │   ├── add_outpatient.html
    │   ├── doctors.html
    │   ├── rooms.html
    │   ├── billing.html
    │   ├── print_inpatients.html
    │   └── print_bills.html
    └── php/              ← API endpoints
        ├── config.php    ← DB connection + requireAuth()
        ├── login.php
        ├── logout.php
        ├── patients.php
        ├── outpatients.php
        ├── doctors.php
        ├── rooms.php
        ├── bills.php
        ├── staff.php
        ├── add_inpatient.php
        ├── add_outpatient.php
        ├── edit_inpatient.php
        ├── edit_outpatient.php
        ├── delete_inpatient.php
        └── delete_outpatient.php
```

---

## 🔐 Security Improvements

The following best practices have been implemented to ensure application security:

* Environment variables used for sensitive data (credentials removed from source code)
* Prepared statements (`bind_param`) used to prevent SQL injection
* Secure authentication system with PHP sessions and bcrypt password hashing
* Server-side authorization enforced on all protected endpoints
* Output sanitization to prevent XSS attacks
* Restricted CORS policy (no wildcard usage)
* Optimized SQL queries with explicit column selection
* Proper error handling for all database operations
* Normalized database schema (removed redundant fields, enforced relationships)
* Auto-incremented primary keys to prevent manual ID manipulation

A complete database schema is included in `schema.sql` for setup.
