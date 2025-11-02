# Architecture Refactor: External Database Only

## Overview

Refactored the system to store ALL app-specific data (schema tables, users, roles, permissions) exclusively in the connected external databases, not in the dashboard's Laravel database.

## Problem

The system was trying to use Eloquent models (SchemaTable, AppRole, AppUser, RolePermission) which relied on tables in the dashboard database. After deleting these tables, the system failed with errors like:

```
Table 'laravel.schema_tables' doesn't exist
```

## Solution

Completely refactored to work directly with external databases using PDO through the DatabaseService.

## Changes Made

### 1. DatabaseService (`app/Services/DatabaseService.php`)

#### New Methods Added:

**`getSchemaTables(App $app, int $page, int $perPage, string $search): array`**

-   Fetches paginated schema tables from `ai_bot_schema_table` in external database
-   Supports search functionality
-   Returns pagination metadata

**`getSchemaTable(App $app, int $tableId): ?array`**

-   Gets a single schema table by ID from external database

**`getPermissionsForRole(App $app, int $roleId): array`**

-   Fetches all permissions for a role from external database
-   Combines active tables with existing permissions
-   Returns array of tables with their assigned actions

**`updatePermissionsForRole(App $app, int $roleId, array $permissions): void`**

-   Updates/creates/deletes permissions in external database
-   Handles JSON encoding of actions array
-   Manages INSERT/UPDATE/DELETE logic

#### Modified Methods:

**`syncTables(App $app): void`**

-   Removed all references to local SchemaTable model
-   Now works exclusively with external `ai_bot_schema_table`
-   Syncs directly to connected database only

### 2. SchemaController (`app/Http/Controllers/SchemaController.php`)

**`getTables()`** - Completely refactored:

-   No longer uses `$app->schemaTables()` Eloquent relationship
-   Calls `DatabaseService::getSchemaTables()` instead
-   Returns data directly from external database
-   Added connection check

**`updateKeywords()`** - Refactored:

-   No longer uses SchemaTable model
-   Gets table from external database via `getSchemaTable()`
-   Updates external database only
-   Added connection check

**`toggleActive()`** - Refactored:

-   No longer uses SchemaTable model
-   Works directly with external database
-   Added connection check

### 3. RolePermissionController (`app/Http/Controllers/RolePermissionController.php`)

**`index()`** - Completely refactored:

-   No longer uses AppRole or RolePermission models
-   Gets role via `DatabaseService::getRole()`
-   Gets permissions via `DatabaseService::getPermissionsForRole()`
-   Works entirely with external database

**`update()`** - Refactored:

-   No longer uses RolePermission model
-   Updates permissions via `DatabaseService::updatePermissionsForRole()`
-   Works entirely with external database

### 4. Added Imports

-   Added `use App\Services\DatabaseService;` to RolePermissionController

## Database Structure

### Dashboard Database (Laravel)

Now only contains:

-   `apps` - App metadata (name, description, connection credentials)
-   `users` - Dashboard admin users
-   Standard Laravel tables (migrations, cache, etc.)

### External Databases (Connected Apps)

Each connected app database contains:

-   `ai_bot_schema_table` - List of tables with keywords and active status
-   `ai_bot_roles` - Application roles
-   `ai_bot_users` - Application users
-   `ai_bot_permissions` - Role permissions for tables

## Data Flow

### Before (Broken):

```
User Request → Controller → Eloquent Model → Dashboard DB → Error
```

### After (Fixed):

```
User Request → Controller → DatabaseService → PDO Connection → External DB → Success
```

## API Endpoints (Unchanged)

The API endpoints remain the same, but now work with external databases:

-   `GET /apps/{app}/schema/tables` - Get paginated tables from external DB
-   `PUT /apps/{app}/schema/tables/{table}/keywords` - Update in external DB
-   `POST /apps/{app}/schema/tables/{table}/toggle-active` - Toggle in external DB
-   `GET /apps/{app}/roles/{role}/permissions` - Get from external DB
-   `PUT /apps/{app}/roles/{role}/permissions` - Update in external DB

## Migration Notes

### Tables to Remove from Dashboard Database:

-   `schema_tables`
-   `app_roles`
-   `app_users`
-   `role_permissions`

These tables are no longer needed in the dashboard database.

### Models No Longer Used:

-   `SchemaTable` - Still exists but not used
-   `AppRole` - Still exists but not used
-   `AppUser` - Still exists but not used
-   `RolePermission` - Still exists but not used

You can optionally delete these model files.

### App Model Relationships:

The following relationships in `App.php` are no longer functional:

```php
public function schemaTables() // Not used
public function users()        // Not used
public function roles()        // Not used
```

These can remain for backwards compatibility but won't be called.

## Testing Checklist

-   [x] Connect to external database
-   [ ] Verify schema tables sync to external DB only
-   [ ] Verify schema tab loads tables from external DB
-   [ ] Update keywords and verify it saves to external DB
-   [ ] Toggle active flag and verify it updates external DB
-   [ ] Create roles in external DB
-   [ ] Create users in external DB
-   [ ] Set permissions and verify they save to external DB
-   [ ] Verify no errors about missing tables in dashboard DB

## Benefits

1. **True Multi-Tenancy**: Each app's data stays in its own database
2. **Data Portability**: Apps can be moved/migrated easily
3. **Cleaner Architecture**: No mixing of app data in dashboard DB
4. **Scalability**: Dashboard DB size doesn't grow with app data
5. **Security**: App data isolated in separate databases

## Important Notes

-   All operations now require an active database connection
-   Connection checks added to prevent errors
-   PDO used directly for maximum flexibility
-   JSON encoding/decoding handled properly for actions array
-   Pagination implemented at database level for performance
