# AI Bot Tables - Quick Reference

## Tables Created Automatically

When connecting a database, these 4 tables are created in the **connected database**:

| Table Name            | Purpose           | Key Columns                       |
| --------------------- | ----------------- | --------------------------------- |
| `ai_bot_schema_table` | Table metadata    | table_name, keywords, active_flag |
| `ai_bot_roles`        | User roles        | role_name, description            |
| `ai_bot_users`        | Application users | name, phone, role_id              |
| `ai_bot_permissions`  | Role permissions  | role_id, permission, actions      |

---

## Quick API Reference

**Base URL:** `/api/apps/{appId}/bot/`

### Health & Stats

```bash
GET  /stats              # Get record counts
GET  /tables/check       # Verify tables exist
```

### Roles

```bash
GET  /roles              # List all roles
POST /roles              # Create role
```

### Users

```bash
GET  /users              # List all users
POST /users              # Create user
```

### Permissions

```bash
GET  /permissions?role_id=1   # List permissions for role
POST /permissions             # Create permission
```

---

## Quick Start

```bash
# 1. Connect database (creates tables automatically)
POST /api/apps/1/connect
{
  "database_type": "mysql",
  "host": "localhost",
  "database_name": "mydb",
  "username": "user",
  "password": "pass"
}

# 2. Create a role
POST /api/apps/1/bot/roles
{"role_name": "Admin", "description": "Full access"}

# 3. Create a user
POST /api/apps/1/bot/users
{"name": "John Doe", "phone": "+123456", "role_id": 1}

# 4. Create permissions
POST /api/apps/1/bot/permissions
{"role_id": 1, "permission": "users.manage", "actions": "CRUD"}
```

---

## Code Examples

### Using DatabaseService

```php
use App\Services\DatabaseService;

// Create all tables
$databaseService->createAllBotTables($app);

// Insert data
$roleId = $databaseService->insertRole($app, $appId, 'Admin', 'Full access');
$userId = $databaseService->insertUser($app, $appId, 'John', '+123', $roleId);
$permId = $databaseService->insertPermission($app, $appId, $roleId, 'users.view', 'read');

// Query data
$roles = $databaseService->getRoles($app, $appId);
$users = $databaseService->getUsers($app, $appId);
$perms = $databaseService->getPermissionsByRole($app, $roleId);

// Check status
$stats = $databaseService->getBotTableStats($app);
$exists = $databaseService->checkBotTablesExist($app);
```

---

## Database Support Matrix

| Feature        | MySQL | PostgreSQL | SQLite | SQL Server |
| -------------- | ----- | ---------- | ------ | ---------- |
| Auto-increment | ✅    | ✅         | ✅     | ✅         |
| Foreign keys   | ✅    | ✅         | ✅     | ✅         |
| Timestamps     | ✅    | ✅         | ✅     | ✅         |
| CASCADE delete | ✅    | ✅         | ✅     | ✅         |

---

## Important Rules

1. ✅ All tables use `ai_bot_` prefix
2. ✅ Tables created in connected database, NOT dashboard database
3. ✅ Create roles before users
4. ✅ Foreign keys enforce data integrity
5. ✅ Tables auto-excluded from schema sync

---

## Common Workflows

### Setup New App

```
1. Connect database → Tables created automatically
2. Create roles → Admin, User, Manager
3. Create permissions → Define what each role can do
4. Create users → Assign roles to users
```

### Add New Feature

```
1. Create permission → "feature.access"
2. Assign to role → Update role permissions
3. Users inherit → All users with that role get access
```

### User Management

```
1. Create user → With role assignment
2. Query users → See role names in results
3. Change role → Update user's role_id
4. Remove role → User's role_id becomes NULL
```

---

## Validation Rules

### Create Role

-   `role_name`: required, string, max 255
-   `description`: optional, string

### Create User

-   `name`: required, string, max 255
-   `phone`: optional, string, max 50
-   `role_id`: optional, integer

### Create Permission

-   `role_id`: required, integer
-   `permission`: required, string, max 255
-   `actions`: optional, string

---

## Error Handling

| Code | Meaning                | Solution                       |
| ---- | ---------------------- | ------------------------------ |
| 400  | Database not connected | Connect database first         |
| 404  | App not found          | Check app ID                   |
| 422  | Validation error       | Fix request data               |
| 500  | Database error         | Check logs, verify permissions |

---

## File Locations

```
app/Services/DatabaseService.php          # Core database logic
app/Http/Controllers/BotDataController.php # API endpoints
routes/api.php                             # Route definitions
DATABASE_SETUP.md                          # Full documentation
BOT_DATA_API.md                           # API reference
DATABASE_SETUP_SUMMARY.md                 # Implementation details
```

---

## Testing Checklist

-   [ ] Connect database
-   [ ] Verify tables exist (`/tables/check`)
-   [ ] Check statistics (`/stats`)
-   [ ] Create role
-   [ ] Create user with role
-   [ ] Create permission
-   [ ] Query all data
-   [ ] Verify foreign keys work
-   [ ] Test with different database types

---

## Need Help?

1. **Full documentation:** `DATABASE_SETUP.md`
2. **API reference:** `BOT_DATA_API.md`
3. **Implementation details:** `DATABASE_SETUP_SUMMARY.md`
4. **Laravel logs:** `storage/logs/laravel.log`
5. **Database logs:** Check your database server logs

---

**Version:** 1.0  
**Last Updated:** November 2, 2025  
**Supported Databases:** MySQL, PostgreSQL, SQLite, SQL Server
