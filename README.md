# Store Management

A PHP and MySQL/MariaDB application for point of sale, stock, purchasing, and reporting.

## Requirements

- PHP 8.4 with the `mysqli`, `session`, and `openssl` extensions. The validator can also use `mbstring` and `iconv`.
- A MySQL or MariaDB database. The fresh installation was verified with MariaDB 11.8.
- Composer 2 for deployment and a writable PHP session directory.

## Install from the shell

1. Create an empty MySQL or MariaDB database and an application database account. Keep the project directory readable by PHP-FPM.
2. From the project directory, copy the template and edit the database settings:

   ```bash
   composer install --no-dev --optimize-autoloader
   cp -n config.php.sample config.php
   nano config.php
   ```

   Keep `config.php` out of Git. On Oracle Linux with an `apache` PHP-FPM worker, an administrator who owns the file can use `chgrp apache config.php` and `chmod 0640 config.php` to grant read access.
3. Set `app_url` to the application URL, including the subfolder, and `mail_from` to an address authorized by your mail relay. Fill in the `smtp` settings before creating the administrator.
4. Import the schema and send your first-admin invitation:

   ```bash
   mariadb -u db_admin -p store_management < schema.sql
   cp -n bin/create-admin.php.sample bin/create-admin.php
   php bin/create-admin.php
   ```

   Enter your email and display name. Open the emailed link within 30 minutes to choose a password. If it expires, rerun the script with the same email. You can remove your local copy of `bin/create-admin.php` after setup. Both it and `config.php` are ignored by Git. For MySQL, substitute `mysql` for `mariadb`.
5. Sign in with your email and password, then open **Store Settings**. For a custom logo, deploy a PNG, JPEG, or GIF into `upload/` from the shell and select it at the bottom of the form.

This procedure is for an empty database. Keep an existing installation or database separate when testing the fresh setup.

## Email delivery and password storage

The invitation and **Forgot your password?** use PHPMailer to connect directly to the SMTP provider. Set the following values in your local `config.php`:

| Setting | Value |
|---|---|
| `app_url` | The HTTPS URL of this app, including its subfolder. See below for local HTTP testing. |
| `mail_from` | A sender address your provider authorizes. |
| `smtp.host`, `smtp.port` | Your provider's SMTP host and port. |
| `smtp.encryption` | `starttls` for STARTTLS (commonly port 587) or `smtps` for implicit TLS (commonly port 465). |
| `smtp.username`, `smtp.password` | Your SMTP login and provider-issued password, app password, or SMTP credential. |

Composer installs the dependencies into `vendor/`. Keep both `vendor/` and the configuration readable by PHP-FPM. Run Composer from your deployment shell. Test both the CLI invitation and the browser reset, since they can run with different permissions. If CLI works but PHP-FPM fails on Oracle Linux, check the PHP and SELinux audit logs for an outbound SMTP denial. If the provider accepts a message and it does not arrive, check its delivery logs and the recipient's spam folder.

### Local HTTP testing

For a trusted development LAN, use a private IPv4 address that the browser can reach. Replace the example IP with your VM address:

```php
'app_url' => 'http://192.168.104.88/store-management',
'allow_local_http' => true,
'mail_from' => 'store@example.com',
```

Local HTTP also accepts `localhost` and loopback IPs, which work when the browser can reach the app on that host. Production should use HTTPS and keep `allow_local_http` set to `false`. HTTP does not protect credentials or reset links in transit, so use the exception only on a trusted local network. SMTP still uses the configured TLS mode in both environments.

Passwords require at least eight characters and allow up to 72 bytes. Passwords use PHP's one-way `password_hash()` API and are checked with `password_verify()`. Invitation and reset links are single-use and expire after 30 minutes. The database stores only a hash of each link token.

The browser does not need write access to the project or logo directory. PHP still needs its normal session storage outside the project.

## Deployment status

The application still uses legacy SQL construction in several inventory and reporting pages. Run it in a private environment until those areas are hardened.

## Store branding

The default header displays Store Management. Set your store name in Store Settings. To use a logo, copy the image into upload/ from the shell and select it in Store Settings. If an existing database still contains your previous company details, update those fields in Store Settings so reports use your current information. If its saved logo file is missing, the header displays Store Management.
