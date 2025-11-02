# Troubleshooting: Missing Dashboard Tables

## Issue Overview

**Error Message:**

```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'laravel.schema_tables' doesn't exist
```

**Cause:**
The dashboard's database tables (`schema_tables`, `app_users`, `app_roles`, `role_permissions`) were manually deleted, but Laravel's migration records still showed them as "Ran".

---

## Understanding the Two Database Layers

### 🗄️ Dashboard Database (Laravel DB)

**Location:** Configured in `config/database.php`
**Purpose:** Manages the Bot Dashboard application itself

**Tables:**

-   `apps` - Stores app configurations and connection details
-   `schema_tables` - **Tracks** metadata about tables in connected databases
-   `app_users` - Dashboard users for each app
-   `app_roles` - Dashboard roles
-   `role_permissions` - Dashboard role permissions
-   `migrations` - Laravel migration tracking
-   `users` - Dashboard admin users
-   `cache`, `jobs`, etc.

### 🗄️ Connected Databases (User's External DB)

**Location:** User-provided connection details (host, database, username, password)
**Purpose:** Stores the actual application data

**AI Bot Tables Created Here:**

-   `ai_bot_schema_table` - Metadata about user's tables
-   `ai_bot_roles` - Application roles in user's DB
-   `ai_bot_users` - Application users in user's DB
-   `ai_bot_permissions` - Application permissions in user's DB
-   **Plus all user's existing tables**

---

## The Problem Explained

When you connect to a database:

```
Dashboard DB                    Connected DB
┌─────────────────┐            ┌──────────────────────┐
│ apps            │            │ ai_bot_schema_table  │
│ schema_tables ←─┼────────────┼─ (tracks tables)    │
│ app_users       │            │ ai_bot_roles         │
│ app_roles       │            │ ai_bot_users         │
│ role_permissions│            │ ai_bot_permissions   │
└─────────────────┘            │ user_table_1         │
                               │ user_table_2         │
                               └──────────────────────┘
```

**What happened:**

1. You deleted `schema_tables` from the **Dashboard DB**
2. The `syncTables()` method tried to use `schema_tables` to track metadata
3. The table didn't exist → Error

**Important:** The `schema_tables` table in the dashboard database is different from `ai_bot_schema_table` in connected databases!

---

## Solution Applied

### Step 1: Remove Migration Records

```bash
php artisan tinker --execute="
  DB::table('migrations')
    ->whereIn('migration', [
      '2025_10_28_112920_create_schema_tables_table',
      '2025_10_29_081752_create_app_users_table',
      '2025_10_29_094443_create_app_roles_table',
      '2025_10_30_100607_create_role_permissions_table',
      '2025_10_30_110126_add_role_id_to_app_users_table',
      '2025_10_30_110134_add_role_id_to_app_users_table'
    ])
    ->delete();
"
```

### Step 2: Re-run Migrations

```bash
php artisan migrate
```

Result: ✅ All tables recreated successfully!

---

## How to Prevent This

### ❌ Don't Do This:

```sql
-- Manually deleting dashboard tables
DROP TABLE schema_tables;
DROP TABLE app_users;
DROP TABLE app_roles;
DROP TABLE role_permissions;
```

### ✅ Instead Do This:

**Option 1: Reset Specific App Data**

```bash
# Delete app and its relationships (cascade delete handles schema_tables)
php artisan tinker --execute="App::find(1)->delete();"
```

**Option 2: Refresh All Migrations**

```bash
php artisan migrate:fresh
# This drops all tables and re-runs migrations cleanly
```

**Option 3: Rollback and Migrate**

```bash
php artisan migrate:rollback
php artisan migrate
```

---

## Common Scenarios

### Scenario 1: Want to Delete an App

```bash
# Use the API or tinker - cascade delete handles related records
DELETE /api/apps/{id}

# Or in tinker:
php artisan tinker
>>> App::find(1)->delete()
```

### Scenario 2: Want Fresh Database

```bash
# Fresh migration (drops everything)
php artisan migrate:fresh

# Or fresh with seeding
php artisan migrate:fresh --seed
```

### Scenario 3: Tables Out of Sync

```bash
# Check what's actually in DB
php artisan migrate:status

# Check if tables exist
php artisan tinker --execute="
  echo 'schema_tables: ' . (Schema::hasTable('schema_tables') ? 'EXISTS' : 'MISSING');
"
```

---

## Quick Reference: What Goes Where?

| Table Name             | Location     | Purpose                        |
| ---------------------- | ------------ | ------------------------------ |
| `apps`                 | Dashboard DB | App connection configs         |
| `schema_tables`        | Dashboard DB | **Tracks** connected DB tables |
| `app_users`            | Dashboard DB | Dashboard users                |
| `app_roles`            | Dashboard DB | Dashboard roles                |
| `role_permissions`     | Dashboard DB | Dashboard permissions          |
| `users`                | Dashboard DB | Admin accounts                 |
| `migrations`           | Dashboard DB | Laravel migration tracking     |
|                        |              |                                |
| `ai_bot_schema_table`  | Connected DB | Metadata about user tables     |
| `ai_bot_roles`         | Connected DB | App roles in user's system     |
| `ai_bot_users`         | Connected DB | App users in user's system     |
| `ai_bot_permissions`   | Connected DB | App permissions                |
| User's existing tables | Connected DB | User's application data        |

---

## Verification Checklist

After fixing, verify everything works:

-   [ ] Tables exist in dashboard DB:

    ```bash
    php artisan db:show
    ```

-   [ ] Can create new app (no errors)

-   [ ] Can connect to database:

    ```bash
    POST /api/apps/{id}/connect
    ```

-   [ ] AI bot tables created in connected DB:

    ```bash
    GET /api/apps/{id}/bot/tables/check
    ```

-   [ ] No errors in Laravel logs:
    ```bash
    tail -f storage/logs/laravel.log
    ```

---

## Key Takeaways

1. **Dashboard DB ≠ Connected DB** - They are separate databases with different purposes

2. **Use Laravel Commands** - Always use `php artisan migrate:*` commands instead of manual SQL

3. **Cascade Deletes Work** - Deleting an app automatically deletes its `schema_tables` records

4. **AI Bot Tables** - These go in the **connected database**, not the dashboard database

5. **Migration Tracking** - Laravel's `migrations` table must stay in sync with actual database state

---

## Support Commands

```bash
# Check migration status
php artisan migrate:status

# Check database structure
php artisan db:show

# Check specific table
php artisan db:table schema_tables

# Run migrations
php artisan migrate

# Fresh start (careful - drops everything!)
php artisan migrate:fresh

# Clear all caches
php artisan optimize:clear
```

---

**Issue:** Manually deleted dashboard tables  
**Solution:** Re-run migrations after clearing migration records  
**Prevention:** Use Laravel/API commands instead of manual SQL  
**Status:** ✅ Resolved
