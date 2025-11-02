# Schema Table Synchronization Update

## Overview

Updated the database synchronization system to save tables to both the local Laravel database and the connected external database's `ai_bot_schema_table`.

## Changes Made

### 1. Enhanced `syncTables()` Method

**File:** `app/Services/DatabaseService.php`

The `syncTables()` method now performs dual synchronization:

-   **Local Database**: Saves schema tables to the Laravel app's `schema_tables` table
-   **External Database**: Saves schema tables to the connected database's `ai_bot_schema_table`

#### Key Features:

-   After successful TCP connection, tables are automatically synced to both databases
-   Skips AI bot system tables (ai*bot*\*)
-   Handles both INSERT and UPDATE operations
-   Cleans up deleted tables from both databases

### 2. New Helper Methods Added

#### `syncTableToExternalDatabase()`

```php
private function syncTableToExternalDatabase(App $app, string $tableName, ?string $keywords, bool $activeFlag): void
```

-   Inserts or updates a table record in the external database's `ai_bot_schema_table`
-   Checks if record exists before deciding to INSERT or UPDATE
-   Sets default values: keywords = '', active_flag = true

#### `deleteTableFromExternalDatabase()`

```php
private function deleteTableFromExternalDatabase(App $app, string $tableName): void
```

-   Removes table records from the external database's `ai_bot_schema_table`
-   Includes error handling to prevent sync failures
-   Logs warnings if deletion fails (table might not exist)

### 3. Import Addition

Added `Illuminate\Support\Facades\Log` to support logging functionality.

## How It Works

### Connection Flow:

1. User clicks "Connect Database" button
2. System validates connection credentials
3. On successful connection:
    - Creates all AI bot tables in external database (including `ai_bot_schema_table`)
    - Calls `syncTables()` to discover all tables
    - Saves each table to both local and external databases
    - Marks app as connected

### Sync Process:

```
Discover Tables → Skip AI Bot Tables → Save to Local DB → Save to External DB
```

### Update Flow:

When users update keywords or active flag:

1. Update local `schema_tables` record
2. Call `updateExternalSchemaTable()` to sync to external database
3. Both databases stay synchronized

## Benefits

1. **Data Persistence**: Tables are stored in the connected database itself
2. **Portability**: External database contains its own schema metadata
3. **Consistency**: Both databases maintain synchronized table information
4. **Error Handling**: Graceful handling of external database failures
5. **Automatic Sync**: No manual intervention required

## Database Schema

### ai_bot_schema_table Structure:

```sql
- id (Primary Key)
- table_name (VARCHAR/TEXT, UNIQUE)
- keywords (TEXT)
- active_flag (BOOLEAN/BIT/INTEGER)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

## Testing Checklist

-   [x] Connect to external database
-   [x] Verify tables sync to both databases
-   [x] Update keywords and verify sync
-   [x] Toggle active flag and verify sync
-   [x] Remove tables and verify cleanup in both databases
-   [ ] Test with different database types (MySQL, PostgreSQL, SQLite, SQL Server)
-   [ ] Verify error handling when external database is unavailable

## Notes

-   The external database connection must be active for sync operations
-   AI bot system tables (ai*bot*\*) are excluded from sync
-   Error logging helps diagnose external database issues
-   Existing `updateExternalSchemaTable()` method continues to work for manual updates
