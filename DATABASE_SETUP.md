# AI Bot Database Setup

## Overview

When a user connects to a database through the Bot Dashboard, the system automatically creates several tables in the **connected database** (not the dashboard's database) to support AI bot functionality.

## Table Naming Convention

All AI bot tables use the `ai_bot_` prefix to avoid conflicts with existing tables:

-   `ai_bot_schema_table`
-   `ai_bot_roles`
-   `ai_bot_users`
-   `ai_bot_permissions`

## Tables Created

### 1. ai_bot_schema_table

Stores metadata about tables in the connected database.

**Columns:**

-   `id` - Primary key (auto-increment)
-   `table_name` - VARCHAR(255), NOT NULL, UNIQUE - Name of the table
-   `keywords` - TEXT - Keywords associated with the table for AI understanding
-   `active_flag` - BOOLEAN - Whether the table is active (default: TRUE)
-   `created_at` - TIMESTAMP - Creation timestamp
-   `updated_at` - TIMESTAMP - Last update timestamp

**Purpose:** Tracks which tables the AI bot can access and their metadata for natural language querying.

---

### 2. ai_bot_roles

Stores role definitions for the application.

**Columns:**

-   `id` - Primary key (auto-increment)
-   `app_id` - INT, NOT NULL - Reference to the application
-   `role_name` - VARCHAR(255), NOT NULL - Name of the role (e.g., "Admin", "User", "Manager")
-   `description` - TEXT - Description of the role's purpose
-   `created_at` - TIMESTAMP - Creation timestamp
-   `updated_at` - TIMESTAMP - Last update timestamp

**Purpose:** Defines different user roles within the application for access control.

---

### 3. ai_bot_users

Stores user information for the application.

**Columns:**

-   `id` - Primary key (auto-increment)
-   `app_id` - INT, NOT NULL - Reference to the application
-   `name` - VARCHAR(255), NOT NULL - User's full name
-   `phone` - VARCHAR(50) - User's phone number
-   `role_id` - INT - Foreign key to ai_bot_roles (nullable)
-   `created_at` - TIMESTAMP - Creation timestamp
-   `updated_at` - TIMESTAMP - Last update timestamp

**Relationships:**

-   Foreign key: `role_id` references `ai_bot_roles(id)` ON DELETE SET NULL

**Purpose:** Manages user accounts within the connected application.

---

### 4. ai_bot_permissions

Stores permission definitions for each role.

**Columns:**

-   `id` - Primary key (auto-increment)
-   `app_id` - INT, NOT NULL - Reference to the application
-   `role_id` - INT, NOT NULL - Foreign key to ai_bot_roles
-   `permission` - VARCHAR(255), NOT NULL - Permission name (e.g., "users.view", "orders.create")
-   `actions` - TEXT - JSON or comma-separated list of allowed actions
-   `created_at` - TIMESTAMP - Creation timestamp
-   `updated_at` - TIMESTAMP - Last update timestamp

**Relationships:**

-   Foreign key: `role_id` references `ai_bot_roles(id)` ON DELETE CASCADE

**Purpose:** Defines what actions each role can perform in the application.

---

## Database Support

The system supports multiple database types:

-   **MySQL/MariaDB**
-   **PostgreSQL**
-   **SQLite**
-   **Microsoft SQL Server**

Each database type has its own optimized SQL syntax for table creation.

---

## Automatic Setup Process

When connecting a database through the API endpoint `/api/apps/{id}/connect`:

1. **Connection Test** - Verifies database credentials and connectivity
2. **Create AI Bot Tables** - Creates all four tables with proper structure
3. **Sync Schema** - Scans existing tables and populates `ai_bot_schema_table`
4. **Mark Connected** - Updates the app status to "connected"

### Example API Request

```http
POST /api/apps/1/connect
Content-Type: application/json

{
  "app_name": "My Application",
  "database_type": "mysql",
  "host": "localhost",
  "port": 3306,
  "database_name": "my_database",
  "username": "db_user",
  "password": "db_password"
}
```

### Example API Response

```json
{
    "success": true,
    "message": "Database connected successfully! All AI bot tables have been created and schema tables synced.",
    "table_count": 15
}
```

---

## Key Features

### 1. Conflict Prevention

The `ai_bot_` prefix ensures no naming conflicts with existing user tables.

### 2. Automatic Exclusion

When syncing tables, the system automatically excludes all `ai_bot_*` tables from being tracked in `ai_bot_schema_table`.

### 3. Foreign Key Relationships

-   Users are linked to roles
-   Permissions are linked to roles with CASCADE delete
-   Orphaned users (deleted role) have `role_id` set to NULL

### 4. Cross-Database Compatibility

All SQL statements are dynamically generated based on the database type to ensure compatibility.

---

## Usage in Code

### Creating All Tables

```php
$databaseService->createAllBotTables($app);
```

### Creating Individual Tables

```php
$databaseService->createSchemaTable($app);
$databaseService->createRolesTable($app);
$databaseService->createUsersTable($app);
$databaseService->createPermissionsTable($app);
```

### Syncing Tables

```php
$databaseService->syncTables($app);
```

### Updating Schema Metadata

```php
$databaseService->updateExternalSchemaTable(
    $app,
    'customers',
    'customer, client, buyer',
    true
);
```

---

## Important Notes

1. **Separate Databases**: All AI bot tables are created in the **user's connected database**, not the dashboard's database.

2. **Table Exclusion**: When syncing, all tables with the `ai_bot_` prefix are automatically excluded from the schema tracking.

3. **Idempotent Operations**: All table creation operations use `IF NOT EXISTS` or equivalent checks, making them safe to run multiple times.

4. **Error Handling**: If tables already exist, the system gracefully handles the duplicate table error and continues operation.

5. **Foreign Keys**: The system properly handles foreign key relationships:
    - `ai_bot_roles` must be created before `ai_bot_users`
    - `ai_bot_roles` must be created before `ai_bot_permissions`

---

## Database Service Methods

Located in: `app/Services/DatabaseService.php`

### Public Methods

-   `testConnection(App $app): bool` - Test database connectivity
-   `createSchemaTable(App $app): void` - Create ai_bot_schema_table
-   `createAllBotTables(App $app): void` - Create all AI bot tables
-   `createRolesTable(App $app): void` - Create ai_bot_roles table
-   `createUsersTable(App $app): void` - Create ai_bot_users table
-   `createPermissionsTable(App $app): void` - Create ai_bot_permissions table
-   `syncTables(App $app): void` - Sync tables from connected database
-   `fetchTables(App $app): array` - Get list of all tables
-   `updateExternalSchemaTable()` - Update schema metadata

### Private Helper Methods

-   `getCreateSchemaTableSQL(string $driver): string`
-   `getCreateRolesTableSQL(string $driver): string`
-   `getCreateUsersTableSQL(string $driver): string`
-   `getCreatePermissionsTableSQL(string $driver): string`
-   `isAIBotTable(string $tableName): bool`
-   `getAIBotTableNames(): array`

---

## Testing

You can test the database setup using the included Postman collection:

-   Import: `Bot_Dashboard_API.postman_collection.json`
-   Environment: `Bot_Dashboard_API.postman_environment.json`
-   Use the "Connect Database" endpoint to trigger table creation

---

## Troubleshooting

### Tables Not Created

-   Check database user permissions (need CREATE TABLE privilege)
-   Verify database connection credentials
-   Check logs in `storage/logs/laravel.log`

### Foreign Key Errors

-   Ensure tables are created in the correct order (roles → users/permissions)
-   For SQLite, ensure foreign key support is enabled

### Duplicate Table Errors

-   These are normal and handled gracefully by the system
-   Tables won't be recreated if they already exist
