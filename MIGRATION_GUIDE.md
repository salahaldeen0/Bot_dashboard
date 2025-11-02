# Migration Guide: Updating Existing Databases

If you have already connected databases using the old `schema_table` naming convention, follow this guide to migrate to the new `ai_bot_*` naming convention.

---

## What Changed?

### Old Naming Convention

-   `schema_table`

### New Naming Convention

-   `ai_bot_schema_table`
-   `ai_bot_roles`
-   `ai_bot_users`
-   `ai_bot_permissions`

---

## Migration Options

### Option 1: Automatic Migration (Recommended)

The system will automatically create the new tables when you reconnect to the database. The old `schema_table` will remain untouched.

**Steps:**

1. Disconnect the app in the dashboard
2. Reconnect using the same credentials
3. New tables will be created with `ai_bot_` prefix
4. Old `schema_table` data can be manually migrated if needed

### Option 2: Manual SQL Migration

Run these SQL commands directly on your connected database to rename/migrate existing tables.

#### For MySQL/MariaDB:

```sql
-- Rename existing table
RENAME TABLE schema_table TO ai_bot_schema_table;

-- Or copy data to new table (if you want to keep both)
CREATE TABLE ai_bot_schema_table LIKE schema_table;
INSERT INTO ai_bot_schema_table SELECT * FROM schema_table;
```

#### For PostgreSQL:

```sql
-- Rename existing table
ALTER TABLE schema_table RENAME TO ai_bot_schema_table;

-- Or copy data to new table
CREATE TABLE ai_bot_schema_table AS SELECT * FROM schema_table;
```

#### For SQLite:

```sql
-- Rename existing table
ALTER TABLE schema_table RENAME TO ai_bot_schema_table;
```

#### For SQL Server:

```sql
-- Rename existing table
EXEC sp_rename 'schema_table', 'ai_bot_schema_table';
```

### Option 3: Fresh Start

If you don't need the old data, simply drop the old table and reconnect:

```sql
DROP TABLE IF EXISTS schema_table;
```

Then reconnect in the dashboard - all new tables will be created.

---

## Data Migration Script

If you need to migrate data from old `schema_table` to new `ai_bot_schema_table`, here's a PHP script you can run:

### Create: `database/migrations/migrate_to_ai_bot_tables.php`

```php
<?php

use App\Models\App;
use App\Services\DatabaseService;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$databaseService = app(DatabaseService::class);

// Get all connected apps
$apps = App::where('is_connected', true)->get();

foreach ($apps as $app) {
    echo "Migrating app: {$app->app_name} (ID: {$app->id})\n";

    try {
        // Create connection
        $pdo = new PDO(
            "mysql:host={$app->host};port={$app->port};dbname={$app->database_name}",
            $app->username,
            decrypt($app->password)
        );

        // Check if old table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'schema_table'");
        $oldTableExists = $stmt->rowCount() > 0;

        // Check if new table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'ai_bot_schema_table'");
        $newTableExists = $stmt->rowCount() > 0;

        if ($oldTableExists && !$newTableExists) {
            echo "  - Found old schema_table, migrating...\n";

            // Rename old table
            $pdo->exec("RENAME TABLE schema_table TO ai_bot_schema_table");
            echo "  ✓ Renamed schema_table to ai_bot_schema_table\n";
        } elseif ($oldTableExists && $newTableExists) {
            echo "  - Both tables exist, copying data...\n";

            // Copy data from old to new
            $pdo->exec("
                INSERT INTO ai_bot_schema_table (table_name, keywords, active_flag, created_at, updated_at)
                SELECT table_name, keywords, active_flag, created_at, updated_at
                FROM schema_table
                WHERE table_name NOT IN (SELECT table_name FROM ai_bot_schema_table)
            ");
            echo "  ✓ Copied data from schema_table to ai_bot_schema_table\n";
        } else {
            echo "  - No migration needed\n";
        }

        // Create additional tables
        $databaseService->createRolesTable($app);
        $databaseService->createUsersTable($app);
        $databaseService->createPermissionsTable($app);
        echo "  ✓ Created additional AI bot tables\n";

    } catch (Exception $e) {
        echo "  ✗ Error: {$e->getMessage()}\n";
    }

    echo "\n";
}

echo "Migration complete!\n";
```

### Run the migration script:

```bash
php database/migrations/migrate_to_ai_bot_tables.php
```

---

## Verification Checklist

After migration, verify everything is working:

### 1. Check Tables Exist

```bash
curl http://localhost:8000/api/apps/1/bot/tables/check
```

Expected response:

```json
{
    "success": true,
    "tables": {
        "ai_bot_schema_table": true,
        "ai_bot_roles": true,
        "ai_bot_users": true,
        "ai_bot_permissions": true
    }
}
```

### 2. Check Data Was Migrated

```bash
curl http://localhost:8000/api/apps/1/bot/stats
```

Expected response shows record counts:

```json
{
    "success": true,
    "data": {
        "roles_count": 0,
        "users_count": 0,
        "permissions_count": 0,
        "schema_tables_count": 10
    }
}
```

If `schema_tables_count` matches your old `schema_table` count, migration was successful.

### 3. Test API Endpoints

```bash
# Create a role
curl -X POST http://localhost:8000/api/apps/1/bot/roles \
  -H "Content-Type: application/json" \
  -d '{"role_name": "Admin", "description": "Test role"}'

# Verify it was created
curl http://localhost:8000/api/apps/1/bot/roles
```

---

## Rollback (If Needed)

If something goes wrong and you need to rollback:

### MySQL/MariaDB:

```sql
RENAME TABLE ai_bot_schema_table TO schema_table;
DROP TABLE IF EXISTS ai_bot_roles;
DROP TABLE IF EXISTS ai_bot_users;
DROP TABLE IF EXISTS ai_bot_permissions;
```

### PostgreSQL:

```sql
ALTER TABLE ai_bot_schema_table RENAME TO schema_table;
DROP TABLE IF EXISTS ai_bot_permissions;
DROP TABLE IF EXISTS ai_bot_users;
DROP TABLE IF EXISTS ai_bot_roles;
```

### SQLite:

```sql
ALTER TABLE ai_bot_schema_table RENAME TO schema_table;
DROP TABLE IF EXISTS ai_bot_permissions;
DROP TABLE IF EXISTS ai_bot_users;
DROP TABLE IF EXISTS ai_bot_roles;
```

### SQL Server:

```sql
EXEC sp_rename 'ai_bot_schema_table', 'schema_table';
DROP TABLE IF EXISTS ai_bot_permissions;
DROP TABLE IF EXISTS ai_bot_users;
DROP TABLE IF EXISTS ai_bot_roles;
```

**Note:** Drop permissions and users before roles due to foreign key constraints.

---

## Common Issues

### Issue: Foreign Key Constraint Error

**Cause:** Trying to drop tables in wrong order

**Solution:** Always drop in this order:

1. `ai_bot_permissions` (has FK to roles)
2. `ai_bot_users` (has FK to roles)
3. `ai_bot_roles` (no FK)
4. `ai_bot_schema_table` (no FK)

### Issue: Table Already Exists

**Cause:** Tables were partially created

**Solution:**

```sql
DROP TABLE IF EXISTS ai_bot_permissions;
DROP TABLE IF EXISTS ai_bot_users;
DROP TABLE IF EXISTS ai_bot_roles;
DROP TABLE IF EXISTS ai_bot_schema_table;
```

Then reconnect in the dashboard.

### Issue: Data Not Migrated

**Cause:** Migration script didn't run or failed

**Solution:** Run migration script with verbose output:

```bash
php database/migrations/migrate_to_ai_bot_tables.php 2>&1 | tee migration.log
```

Check `migration.log` for errors.

---

## Best Practices

1. **Backup First**: Always backup your database before migration
2. **Test on Staging**: Test the migration on a non-production database first
3. **Migration Window**: Schedule migration during low-traffic periods
4. **Verify Data**: Check record counts before and after migration
5. **Keep Old Tables**: Keep old `schema_table` for a few days before dropping
6. **Document Changes**: Note which apps were migrated and when

---

## Database-Specific Backup Commands

### MySQL/MariaDB:

```bash
mysqldump -u username -p database_name schema_table > backup_schema_table.sql
```

### PostgreSQL:

```bash
pg_dump -U username -d database_name -t schema_table > backup_schema_table.sql
```

### SQLite:

```bash
sqlite3 database.db ".dump schema_table" > backup_schema_table.sql
```

### SQL Server:

```sql
-- Right-click table → Tasks → Generate Scripts
```

---

## Migration Timeline Recommendation

### Phase 1: Preparation (Day 1)

-   Review current database structure
-   Backup all databases
-   Test migration script on development
-   Notify users of upcoming changes

### Phase 2: Migration (Day 2)

-   Run migration script on staging
-   Verify data integrity
-   Test all API endpoints
-   Fix any issues found

### Phase 3: Production (Day 3-7)

-   Run migration on production during maintenance window
-   Monitor for errors
-   Keep old tables for 7 days as backup
-   Update documentation

### Phase 4: Cleanup (Day 8+)

-   Verify everything working correctly
-   Drop old `schema_table` if not needed
-   Remove migration scripts
-   Archive backups

---

## Support

If you encounter issues during migration:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check database error logs
3. Review migration script output
4. Test with `php artisan tinker`:
    ```php
    $app = App::find(1);
    $service = app(\App\Services\DatabaseService::class);
    $service->checkBotTablesExist($app);
    ```

---

## Summary

The migration process is straightforward:

1. Backup your data
2. Choose migration method (auto/manual/fresh)
3. Run migration
4. Verify tables and data
5. Test API endpoints
6. Clean up old tables (after verification period)

The new `ai_bot_*` prefix ensures no conflicts with existing tables and provides a clear namespace for all AI bot functionality.
