# Store Management

A PHP and MySQL/MariaDB application for point of sale, stock, purchasing, and reporting.

## Requirements

- PHP 8.4 with the `mysqli` and `session` extensions. The validator can also use `mbstring` and `iconv`.
- A MySQL or MariaDB database. The fresh installation was verified with MariaDB 11.8.
- A writable PHP session directory. For the web installer, PHP also needs permission to create `config.php` in the project directory.

## Run locally on Windows

Start MySQL or MariaDB, open PowerShell in the extracted project directory, and run:

```powershell
php -v
php -S 127.0.0.1:8000
```

Open <http://127.0.0.1:8000>. Enter your database connection details, create a database or select an empty one, then set the administrator login and store details. The installer uses `schema.sql` and starts with empty inventory and sales.

## Upgrade an existing database

1. Back up your database and test with a restored copy.
2. If the copy matches the schema in `dipak_sample_pos_starting.sql` (2017), run `upgrade-2017-schema.sql` once on the copy. It adds the sales barcode column and defaults for fields that the application omits during inserts. If your schema differs, compare it with `schema.sql` and review which changes apply before running any SQL.
3. Copy `config.php.sample` to `config.php` and enter the test database connection details. Existing installations can also reuse their original `config.php`.
4. Start the app, sign in with your existing account, and verify stock counts, purchases, sales, and reports against the backup copy.

The historical dumps were removed from the project. Keep any database backup outside the web root. Use `schema.sql` for a fresh database.

## Deployment status

This is a PHP compatibility upgrade. The application still stores passwords in plaintext and uses legacy SQL construction in several pages. Run it on localhost or in a private test environment until those areas are hardened.

## Store branding

The default header displays Store Management. Set your own store name and upload a logo in Store Settings. If an existing database still contains your previous company details, update those fields in Store Settings so reports use your current information. If its saved logo file is missing, the header displays Store Management.
