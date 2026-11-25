# Store Management

A PHP and MySQL/MariaDB application for point of sale, stock, purchasing, and reporting.

## Requirements

- PHP 8.4 with the `mysqli` and `session` extensions. The validator can also use `mbstring` and `iconv`.
- A MySQL or MariaDB database. The fresh installation was verified with MariaDB 11.8.
- A writable PHP session directory.

## Fresh installation

1. Create an **empty** database in phpMyAdmin or your database administration tool. Give the app's database account access to that database, including permission to create and alter its tables during setup.
2. From the project folder on Oracle Linux, create the local configuration as the deployment administrator. Do this only for a new installation:

   ```bash
   cp -n config.php.sample config.php
   php -r 'echo bin2hex(random_bytes(24)), PHP_EOL;'
   ```

   Edit `config.php`. Set `database`, `host`, `username`, and `password`, then paste the generated value into `setup_token`. PHP-FPM needs read access to the file. For the `apache` worker on Oracle Linux, you can use `chgrp apache config.php` and `chmod 0640 config.php` from your administrator shell.
3. Open `/store-management/install.php` in your browser. Enter your setup token. The wizard creates the tables, prompts you to create the first administrator, and opens Store Settings.
4. Save your store details. Clear `setup_token` in your local configuration after setup. Keep `config.php` out of Git.

On Windows PowerShell, use `Copy-Item config.php.sample config.php` on a fresh checkout. The same PHP command generates the token. For local testing, run `php -S 127.0.0.1:8000` in the project directory and open <http://127.0.0.1:8000/install.php>.

The web installer only reads the project directory. Leave its standard Apache and SELinux permissions in place. A custom logo is optional and requires write access to `upload/` only.

### Resume setup

If a previous installer created all the tables but failed before creating the first user, point `config.php` at that database and open `install.php`. The wizard reuses the complete schema. For a partially created schema, use a new empty database. If an account already exists, sign in and finish the profile in Store Settings.

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
