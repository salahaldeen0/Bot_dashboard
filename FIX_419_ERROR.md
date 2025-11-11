# 419 CSRF Error Fix - Deployment Checklist

## Problem
419 Page Expired error occurs after clicking sign-in on production server (ip-teamway.com).

## Root Causes
1. Missing sessions table in database
2. Incorrect production environment configuration
3. Storage directory permissions issues
4. HTTPS/Cookie configuration mismatch

## Solution Steps

### On Your Server (via SSH or FTP):

#### 1. Update Environment Configuration
Replace `.env` file on server with `.env.production` contents:
- Set `APP_ENV=production`
- Set `APP_DEBUG=false`
- Set `APP_URL=https://ip-teamway.com` (or `http://` if no SSL)
- Set `SESSION_DOMAIN=.ip-teamway.com`
- Set `SESSION_SECURE_COOKIE=true` (if using HTTPS) or `false` (if using HTTP)

#### 2. Run Database Migration for Sessions Table
```bash
php artisan migrate
```

This will create the `sessions` table needed for session storage.

#### 3. Clear All Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

#### 4. Optimize for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 5. Set Directory Permissions
Ensure these directories are writable by the web server:
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

Or if using cPanel/Plesk:
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

#### 6. Verify Database Connection
Make sure database credentials in `.env` are correct for production server.

## Quick Fix If You Don't Have SSH Access:

### Option A: Use File-Based Sessions Instead
In `.env` on server:
```
SESSION_DRIVER=file
```
Then manually delete contents of:
- `storage/framework/sessions/`
- `storage/framework/cache/`
- `storage/framework/views/`

### Option B: Create Sessions Table via phpMyAdmin
Run this SQL query in phpMyAdmin:

```sql
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Important Notes:

1. **HTTPS vs HTTP**: If your server uses HTTPS, set `SESSION_SECURE_COOKIE=true`. If HTTP only, set it to `false`.

2. **Session Domain**: 
   - For `https://ip-teamway.com`, use `SESSION_DOMAIN=.ip-teamway.com`
   - Include the leading dot (`.`) to support subdomains

3. **Storage Permissions**: The `storage` and `bootstrap/cache` directories MUST be writable by the web server.

4. **After Changes**: Always clear caches after making configuration changes.

## Testing Steps:

1. Clear browser cookies and cache
2. Visit the sign-in page
3. Open browser Developer Tools (F12) → Network tab
4. Submit the login form
5. Check if there's a valid session cookie being set
6. Verify CSRF token is present in the form

## Common Issues:

- **Still getting 419**: Clear browser cookies completely and try again
- **Mixed Content**: If some assets load via HTTP and some via HTTPS, browser may block cookies
- **Wrong APP_URL**: Must match exactly how users access the site (including http/https)
- **Storage not writable**: Check file permissions on server

## Files to Upload to Server:
- `.env` (with production settings)
- `database/migrations/2025_11_09_000000_create_sessions_table.php` (new file)

Then run migrations and cache clearing commands on server.
