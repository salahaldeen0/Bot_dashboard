# Bot Data API Endpoints

## Overview

These endpoints allow you to manage roles, users, and permissions in the connected database's AI bot tables.

All endpoints require the app to be connected to a database. If not connected, you'll receive a 400 error.

---

## Statistics & Health Check

### Get Bot Table Statistics

Get counts of records in all AI bot tables.

**Endpoint:** `GET /api/apps/{appId}/bot/stats`

**Response:**

```json
{
    "success": true,
    "data": {
        "roles_count": 5,
        "users_count": 23,
        "permissions_count": 15,
        "schema_tables_count": 42
    }
}
```

---

### Check Bot Tables Existence

Verify which AI bot tables exist in the connected database.

**Endpoint:** `GET /api/apps/{appId}/bot/tables/check`

**Response:**

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

---

## Role Management

### Get All Roles

Retrieve all roles for an app.

**Endpoint:** `GET /api/apps/{appId}/bot/roles`

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "app_id": 1,
            "role_name": "Admin",
            "description": "Full system access",
            "created_at": "2025-11-02 10:30:00",
            "updated_at": "2025-11-02 10:30:00"
        },
        {
            "id": 2,
            "app_id": 1,
            "role_name": "User",
            "description": "Standard user access",
            "created_at": "2025-11-02 10:31:00",
            "updated_at": "2025-11-02 10:31:00"
        }
    ]
}
```

---

### Create a Role

Create a new role in the connected database.

**Endpoint:** `POST /api/apps/{appId}/bot/roles`

**Request Body:**

```json
{
    "role_name": "Manager",
    "description": "Can manage team and view reports"
}
```

**Validation Rules:**

-   `role_name`: required, string, max 255 characters
-   `description`: optional, string

**Response:**

```json
{
    "success": true,
    "message": "Role created successfully.",
    "role_id": 3
}
```

**Error Response (400):**

```json
{
    "success": false,
    "message": "Database is not connected."
}
```

---

## User Management

### Get All Users

Retrieve all users for an app with their role information.

**Endpoint:** `GET /api/apps/{appId}/bot/users`

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "app_id": 1,
            "name": "John Doe",
            "phone": "+1234567890",
            "role_id": 1,
            "role_name": "Admin",
            "created_at": "2025-11-02 10:30:00",
            "updated_at": "2025-11-02 10:30:00"
        },
        {
            "id": 2,
            "app_id": 1,
            "name": "Jane Smith",
            "phone": "+1234567891",
            "role_id": 2,
            "role_name": "User",
            "created_at": "2025-11-02 10:32:00",
            "updated_at": "2025-11-02 10:32:00"
        }
    ]
}
```

---

### Create a User

Create a new user in the connected database.

**Endpoint:** `POST /api/apps/{appId}/bot/users`

**Request Body:**

```json
{
    "name": "Bob Johnson",
    "phone": "+1234567892",
    "role_id": 2
}
```

**Validation Rules:**

-   `name`: required, string, max 255 characters
-   `phone`: optional, string, max 50 characters
-   `role_id`: optional, integer (must reference existing role)

**Response:**

```json
{
    "success": true,
    "message": "User created successfully.",
    "user_id": 3
}
```

---

## Permission Management

### Get Permissions by Role

Retrieve all permissions assigned to a specific role.

**Endpoint:** `GET /api/apps/{appId}/bot/permissions`

**Query Parameters:**

-   `role_id`: required, integer

**Example:** `GET /api/apps/1/bot/permissions?role_id=1`

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "app_id": 1,
            "role_id": 1,
            "permission": "users.view",
            "actions": "read,list",
            "created_at": "2025-11-02 10:30:00",
            "updated_at": "2025-11-02 10:30:00"
        },
        {
            "id": 2,
            "app_id": 1,
            "role_id": 1,
            "permission": "users.manage",
            "actions": "create,update,delete",
            "created_at": "2025-11-02 10:30:00",
            "updated_at": "2025-11-02 10:30:00"
        }
    ]
}
```

---

### Create a Permission

Create a new permission for a role.

**Endpoint:** `POST /api/apps/{appId}/bot/permissions`

**Request Body:**

```json
{
    "role_id": 2,
    "permission": "orders.view",
    "actions": "read,list"
}
```

**Validation Rules:**

-   `role_id`: required, integer
-   `permission`: required, string, max 255 characters
-   `actions`: optional, string (comma-separated actions or JSON)

**Response:**

```json
{
    "success": true,
    "message": "Permission created successfully.",
    "permission_id": 3
}
```

---

## Error Responses

### Database Not Connected (400)

```json
{
    "success": false,
    "message": "Database is not connected."
}
```

### App Not Found (404)

```json
{
    "message": "No query results for model [App\\Models\\App] 123"
}
```

### Server Error (500)

```json
{
    "success": false,
    "message": "Failed to create role: SQLSTATE[23000]: Integrity constraint violation"
}
```

### Validation Error (422)

```json
{
    "message": "The role name field is required.",
    "errors": {
        "role_name": ["The role name field is required."]
    }
}
```

---

## Usage Examples

### Complete Workflow Example

```bash
# 1. Check if bot tables exist
curl -X GET http://localhost:8000/api/apps/1/bot/tables/check

# 2. Get current statistics
curl -X GET http://localhost:8000/api/apps/1/bot/stats

# 3. Create roles
curl -X POST http://localhost:8000/api/apps/1/bot/roles \
  -H "Content-Type: application/json" \
  -d '{
    "role_name": "Admin",
    "description": "Full system access"
  }'

curl -X POST http://localhost:8000/api/apps/1/bot/roles \
  -H "Content-Type: application/json" \
  -d '{
    "role_name": "User",
    "description": "Standard user access"
  }'

# 4. Create permissions for Admin role
curl -X POST http://localhost:8000/api/apps/1/bot/permissions \
  -H "Content-Type: application/json" \
  -d '{
    "role_id": 1,
    "permission": "users.manage",
    "actions": "create,read,update,delete"
  }'

# 5. Create a user
curl -X POST http://localhost:8000/api/apps/1/bot/users \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "phone": "+1234567890",
    "role_id": 1
  }'

# 6. Get all users
curl -X GET http://localhost:8000/api/apps/1/bot/users

# 7. Get permissions for a role
curl -X GET "http://localhost:8000/api/apps/1/bot/permissions?role_id=1"
```

---

## Notes

1. **App ID**: Replace `{appId}` with the actual app ID from your database
2. **Foreign Keys**: When creating users, ensure the `role_id` references an existing role
3. **Cascading Deletes**: Deleting a role will delete all associated permissions
4. **NULL Roles**: Users can exist without a role (role_id = NULL)
5. **Actions Format**: The `actions` field can be a comma-separated string or JSON
6. **Database Types**: All operations work across MySQL, PostgreSQL, SQLite, and SQL Server

---

## Testing with Postman

1. Import the collection: `Bot_Dashboard_API.postman_collection.json`
2. Set environment variables:
    - `base_url`: http://localhost:8000
    - `app_id`: Your app ID
3. Test endpoints in this order:
    - Check Tables
    - Get Stats
    - Create Role
    - Create User
    - Create Permission
    - Get all data
