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

## Security fixes applied
| Issue | Fix |
|---|---|
| Live DB credentials in source | Moved to `.env`, `.env` gitignored |
| SQL injection in delete cascade | All queries use `bind_param()` |
| Hardcoded JS login | Replaced with PHP session + bcrypt |
| No server-side auth on endpoints | `requireAuth()` called at top of every PHP file |
| XSS via innerHTML | All rendering uses `document.createTextNode()` via `esc()` helper |
| CORS wildcard | Scoped to `http://localhost` |
| SELECT * in staff.php | Explicit column selection |
| Duplicate patient rows (outpatients) | Fixed with `GROUP_CONCAT` + `GROUP BY` |
| Manual admissionID / patientid | Removed from forms; columns are AUTO_INCREMENT |
| doctorname stored redundantly | Removed from OutPatient; fetched via JOIN |
| onclick string injection for apostrophes | Replaced with `addEventListener` + object passing |
| No $result error handling | All GET endpoints check `$result === false` |
| No SQL schema file | `schema.sql` added with full DDL + seed data |
