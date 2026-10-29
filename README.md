# BarangayHub — Resident Management System

A responsive PHP + MySQL resident registry built for the PDO/OOP midterm requirements. Includes login sessions, resident CRUD, multi-field search, validation, CSRF tokens, an activity log, and database transactions for create/update/delete.

## Requirements
- XAMPP (Apache + MySQL)
- PHP 8.1+ with PDO MySQL and mbstring enabled
- MySQL / MariaDB
- A modern browser

## Install in XAMPP
1. Extract the `barangay_resident_management` folder into `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.
3. Open `config/database.php`. The defaults assume MySQL at `localhost`, username `root`, blank password, and database `barangay_resident_db`. Adjust these values if your local MySQL setup differs.
4. Open `http://localhost/phpmyadmin/`, create a database named `barangay_resident_db`.
5. Visit `http://localhost/barangay_resident_management/setup.php` once. It creates the tables and the initial admin account.
6. Sign in at `http://localhost/barangay_resident_management/login.php`.

### Initial local login
- Username: `admin`
- Password: `Admin@12345`

Change/remove the default account before using this with real data. Delete or rename `setup.php` after successful setup.

## Main workflow
Login → Dashboard → Resident records → Add/View/Edit/Delete/Search → MySQL.
All pages except login/setup are protected by a session guard. Create/update/delete use PDO prepared statements and wrap data changes plus activity logging in transactions with rollback on errors.

## Main files
- `classes/Resident.php` — OOP resident model and PDO methods: `addResident`, `getResidents`, `updateResident`, `deleteResident`, `searchResidents`.
- `config/database.php` — PDO connection.
- `includes/auth.php` — session guard, CSRF, output escaping helpers.
- `resident-action.php` — validates and routes POST actions.
- `assets/css/style.css` — responsive green UI.
- `setup.php` — creates database tables and default account.

## Database fields
`resident_id`, `first_name`, `middle_name`, `last_name`, `sex`, `birth_date`, `phone`, `purok`, `address`, `created_at`, `updated_at`.

These are a reasonable implementation of a resident registry; if your approved ERD specifies different fields, update the form, class, and SQL schema to match it exactly.

## Submission: GitHub
1. Create a new repository, e.g. `barangayhub-resident-management`.
2. Upload the project files (do not upload database passwords or real resident data).
3. Make the repository public or invite your instructor's GitHub account `@carljoshy`.
4. Put your GitHub repository URL and deployed application URL in your submission.

## Deployment
PHP/MySQL needs a PHP-capable hosting service. A static hosting service such as GitHub Pages will not run PHP. Configure your host's database credentials and import/create the tables before sharing the deployed URL. Do not publish the local XAMPP URL as a public deployment link.

## Before submitting
- [ ] Test login and logout.
- [ ] Test adding, viewing, editing, deleting, and searching residents.
- [ ] Verify create/update/delete and activity logging work.
- [ ] Confirm the project matches the fields in your approved ERD.
- [ ] Remove setup.php after initialization and change default credentials.
- [ ] Upload source files to GitHub.
- [ ] Deploy to a PHP/MySQL host and attach both links.


## Import the SQL file
You can import `database.sql` in phpMyAdmin (select Import and choose the file). It creates the database and tables. Then open `setup.php` once to create the default administrator account using PHP password hashing.
