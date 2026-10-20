# Bautista_Final_Project

A PHP-based pharmacy inventory management system with:
- Admin login/dashboard
- User login/dashboard
- Medicine add/edit/delete actions
- Stock and pricing tracking
- Medicine request workflow
- Simple inventory graph

## Quick Start

1. Make sure PHP is installed.
2. Start a local PHP server from the project root:
   ```bash
   php -S localhost:8000
   ```
3. Open `http://localhost:8000/login.php`

## Default admin login

- Email: `admin@pharmacy.local`
- Password: `admin123`

## Database

The project uses SQLite by default, which keeps the app easy to run without external services. If a MySQL environment is configured via `DB_DRIVER`, `DB_HOST`, `DB_NAME`, `DB_USERNAME`, and `DB_PASSWORD`, the system will use MySQL automatically.

## Files

- `dbconnection.php` - shared database bootstrap and table initialization
- `admin-dashboard.php` - admin inventory overview
- `user-dashboard.php` - customer inventory view and requests
- `add_medicine.php` - add items to stock
- `request_medicine.php` - request items
- `view_graph.php` - simple category stock chart
- `inventory_management.sql` - schema reference

## Notes

- The database file is created automatically on first run.
- Existing data is preserved when the app runs again.
- Passwords are stored with PHP `password_hash()`.

