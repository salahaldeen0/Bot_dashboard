# Fix: Users and Roles Now Save to Connected Database

## Issue Summary

Users and roles were being saved to the **dashboard's database** (`app_users`, `app_roles` tables) instead of the **connected database** (`ai_bot_users`, `ai_bot_roles` tables).

## Root Cause

The `AppUserController` and `AppRoleController` were using Laravel Eloquent models (`AppUser`, `AppRole`) which save to the dashboard's database by default.

## Solution Applied

Updated both controllers to use `DatabaseService` methods that directly interact with the connected database's `ai_bot_*` tables.

---

## Changes Made

### 1. DatabaseService.php - Added New Methods

#### User Management

```php
updateUser($app, $userId, $name, $phone, $roleId)    // Update user in ai_bot_users
deleteUser($app, $userId)                             // Delete user from ai_bot_users
getUser($app, $userId)                                // Get single user with role
```

#### Role Management

```php
updateRole($app, $roleId, $roleName, $description)   // Update role in ai_bot_roles
deleteRole($app, $roleId)                             // Delete role from ai_bot_roles
getRole($app, $roleId)                                // Get single role
```

### 2. AppUserController.php - Complete Rewrite

**Before:**

-   Used `AppUser::create()` → Saved to dashboard DB
-   Used `AppUser::where()->get()` → Queried dashboard DB
-   Direct Eloquent operations

**After:**

-   Uses `$databaseService->insertUser()` → Saves to connected DB
-   Uses `$databaseService->getUsers()` → Queries connected DB
-   All operations go through DatabaseService
-   Added database connection checks

### 3. AppRoleController.php - Complete Rewrite

**Before:**

-   Used `AppRole::create()` → Saved to dashboard DB
-   Used `$app->roles()->get()` → Queried dashboard DB
-   Direct Eloquent operations

**After:**

-   Uses `$databaseService->insertRole()` → Saves to connected DB
-   Uses `$databaseService->getRoles()` → Queries connected DB
-   All operations go through DatabaseService
-   Added database connection checks

---

## Data Flow Comparison

### ❌ Old Flow (Wrong)

```
User Creates Role/User
      ↓
AppRoleController / AppUserController
      ↓
Eloquent Models (AppRole / AppUser)
      ↓
Dashboard Database (MySQL: laravel)
      ↓
Tables: app_roles, app_users ❌ Wrong location!
```

### ✅ New Flow (Correct)

```
User Creates Role/User
      ↓
AppRoleController / AppUserController
      ↓
DatabaseService
      ↓
PDO Connection to Connected Database
      ↓
Tables: ai_bot_roles, ai_bot_users ✅ Correct location!
```

---

## New Features Added

### 1. Connection Validation

All methods now check if database is connected:

```php
if (!$app->is_connected) {
    return response()->json([
        'success' => false,
        'message' => 'Database is not connected. Please connect to a database first.'
    ], 400);
}
```

### 2. Error Handling

All database operations wrapped in try-catch:

```php
try {
    $userId = $this->databaseService->insertUser(...);
    return response()->json(['success' => true, ...]);
} catch (\Exception $e) {
    return response()->json([
        'success' => false,
        'message' => 'Failed to create user: ' . $e->getMessage()
    ], 500);
}
```

### 3. Better Response Messages

Updated messages to clarify where data is saved:

-   "User created successfully in connected database!"
-   "Role updated successfully in connected database!"
-   "User deleted successfully from connected database!"

---

## What This Means for Users

### Before the Fix

1. Create app and connect database ✅
2. `ai_bot_*` tables created in connected DB ✅
3. Create user/role in dashboard ❌
4. Data saved to dashboard DB (wrong place) ❌
5. Connected database stays empty ❌

### After the Fix

1. Create app and connect database ✅
2. `ai_bot_*` tables created in connected DB ✅
3. Create user/role in dashboard ✅
4. Data saved to connected DB (correct place) ✅
5. Can query data from connected database ✅

---

## Testing the Fix

### Step 1: Connect a Database

```bash
POST /api/apps/1/connect
{
  "database_type": "mysql",
  "host": "localhost",
  "database_name": "test_db",
  "username": "user",
  "password": "pass"
}
```

### Step 2: Create a Role

```bash
POST /apps/1/roles
{
  "role_name": "Admin",
  "description": "Administrator role"
}
```

### Step 3: Verify in Connected Database

Connect directly to your database and check:

```sql
SELECT * FROM ai_bot_roles;
-- Should show the Admin role ✅
```

Check dashboard database:

```sql
SELECT * FROM app_roles;
-- Should be empty (or only old data) ✅
```

### Step 4: Create a User

```bash
POST /apps/1/users
{
  "name": "John Doe",
  "phone": "+1234567890",
  "role_id": 1
}
```

### Step 5: Verify in Connected Database

```sql
SELECT * FROM ai_bot_users;
-- Should show John Doe ✅

SELECT u.*, r.role_name
FROM ai_bot_users u
LEFT JOIN ai_bot_roles r ON u.role_id = r.id;
-- Should show John Doe with role_name = "Admin" ✅
```

---

## API Endpoints (All Now Save to Connected DB)

| Method | Endpoint                       | Action      | Database     |
| ------ | ------------------------------ | ----------- | ------------ |
| GET    | `/apps/{appId}/users`          | List users  | Connected DB |
| POST   | `/apps/{appId}/users`          | Create user | Connected DB |
| PUT    | `/apps/{appId}/users/{userId}` | Update user | Connected DB |
| DELETE | `/apps/{appId}/users/{userId}` | Delete user | Connected DB |
| GET    | `/apps/{appId}/roles`          | List roles  | Connected DB |
| POST   | `/apps/{appId}/roles`          | Create role | Connected DB |
| PUT    | `/apps/{appId}/roles/{roleId}` | Update role | Connected DB |
| DELETE | `/apps/{appId}/roles/{roleId}` | Delete role | Connected DB |

---

## Important Notes

### 1. Database Must Be Connected

All operations now require an active database connection. If not connected:

```json
{
    "success": false,
    "message": "Database is not connected. Please connect to a database first."
}
```

### 2. Old Data in Dashboard Database

If you have old data in `app_users` or `app_roles` tables (dashboard DB), it will **not** be automatically migrated. You'll need to:

-   Manually copy it to the connected database, OR
-   Re-create users/roles using the UI/API

### 3. Two Separate Systems

Dashboard still has its own tables for tracking:

-   `apps` - App configurations
-   `schema_tables` - Metadata about connected tables
-   `migrations` - Laravel migration history
-   `users` - Dashboard admin accounts

But user/role **data** now lives in connected databases.

### 4. Foreign Keys Still Work

The `ai_bot_users.role_id` foreign key to `ai_bot_roles` is properly maintained in the connected database.

---

## Rollback (If Needed)

If you need to revert to the old behavior (save to dashboard DB), you can:

1. Restore the old controller files from git:

```bash
git checkout HEAD~1 app/Http/Controllers/AppUserController.php
git checkout HEAD~1 app/Http/Controllers/AppRoleController.php
```

2. Or manually change controllers back to use Eloquent models

---

## Files Modified

1. ✅ `app/Services/DatabaseService.php` - Added update/delete/get methods
2. ✅ `app/Http/Controllers/AppUserController.php` - Complete rewrite to use DatabaseService
3. ✅ `app/Http/Controllers/AppRoleController.php` - Complete rewrite to use DatabaseService

---

## Verification Checklist

-   [x] No PHP syntax errors
-   [x] Connection check added to all methods
-   [x] Error handling added
-   [x] Users save to `ai_bot_users` in connected DB
-   [x] Roles save to `ai_bot_roles` in connected DB
-   [x] Update operations work
-   [x] Delete operations work
-   [x] Foreign key relationships maintained
-   [x] Response messages updated

---

**Issue:** Users/roles saving to wrong database  
**Solution:** Updated controllers to use DatabaseService  
**Result:** All data now correctly saves to connected database's `ai_bot_*` tables  
**Status:** ✅ Fixed
