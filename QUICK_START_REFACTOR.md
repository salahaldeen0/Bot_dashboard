# Quick Start Guide - After Architecture Refactor

## What Changed?

The system no longer stores app-specific data in the dashboard's Laravel database. All data (schema tables, users, roles, permissions) is now stored exclusively in each app's connected external database.

## Immediate Steps

### 1. Clear Browser Cache

Clear your browser cache and reload the page to get the updated JavaScript file.

### 2. Test the Flow

#### Step 1: Create an App

1. Go to Apps dashboard
2. Click "Create New App"
3. Enter app details
4. Save

#### Step 2: Connect to Database

1. Edit the app you just created
2. Fill in database connection details:
    - Database Type (MySQL, PostgreSQL, etc.)
    - Database Name
    - Host
    - Port
    - Username
    - Password
3. Click "Connect Database"

**Expected Result:**

-   Connection successful message
-   Tables automatically synced
-   Schema tab unlocked

#### Step 3: View Schema Tables

1. Click on "Schema" tab
2. Should see all tables from connected database
3. AI bot tables (ai*bot*\*) are hidden

**Expected Result:**

-   Tables displayed from external database's `ai_bot_schema_table`
-   Can update keywords
-   Can toggle active/inactive

#### Step 4: Add Users

1. Click on "Users" tab (unlocked after schema sync)
2. Click "Add User"
3. Enter name and phone
4. Save

**Expected Result:**

-   User saved to external database's `ai_bot_users` table
-   Users count incremented

#### Step 5: Create Roles

1. Click on "Roles" tab (unlocked after adding users)
2. Click "Add Role"
3. Enter role name and description
4. Save

**Expected Result:**

-   Role saved to external database's `ai_bot_roles` table

#### Step 6: Set Permissions

1. Click on "Permissions" tab
2. Select a role from dropdown
3. Check/uncheck permissions for each table
4. Click "Save Permissions"

**Expected Result:**

-   Permissions saved to external database's `ai_bot_permissions` table
-   Console shows debug logs

## Troubleshooting

### Error: "Table 'laravel.schema_tables' doesn't exist"

**Solution:** This error should no longer appear. If it does, make sure you've:

1. Cleared browser cache
2. Refreshed the page (hard refresh: Ctrl+Shift+R)
3. Verified the changes were applied

### Error: "Database not connected"

**Solution:** Click "Connect Database" in the App Details tab first.

### Tables not showing in Schema tab

**Check:**

1. Database connection is active (green "Connected" badge)
2. Click "Sync Tables" button
3. Check browser console for errors (F12)

### Permissions not loading

**Check:**

1. Role is selected in dropdown
2. Schema has been synced (tables exist)
3. Browser console for debug messages

## Database Verification

To verify data is being stored correctly in external database, connect to it and run:

```sql
-- Check schema tables
SELECT * FROM ai_bot_schema_table;

-- Check users
SELECT * FROM ai_bot_users;

-- Check roles
SELECT * FROM ai_bot_roles;

-- Check permissions
SELECT * FROM ai_bot_permissions;
```

## What Was Removed

These tables were removed from dashboard database (laravel):

-   `schema_tables`
-   `app_roles`
-   `app_users`
-   `role_permissions`

They are no longer needed. All data is in external databases now.

## API Changes

No API endpoint changes! All endpoints work the same:

-   `GET /apps/{app}/schema/tables` ✅
-   `POST /apps/{app}/schema/sync` ✅
-   `PUT /apps/{app}/schema/tables/{table}/keywords` ✅
-   `POST /apps/{app}/schema/tables/{table}/toggle-active` ✅
-   `GET /apps/{app}/users` ✅
-   `POST /apps/{app}/users` ✅
-   `GET /apps/{app}/roles` ✅
-   `POST /apps/{app}/roles` ✅
-   `GET /apps/{app}/roles/{role}/permissions` ✅
-   `PUT /apps/{app}/roles/{role}/permissions` ✅

## Console Debugging

When testing permissions tab, check browser console (F12) for these messages:

-   "Loading permissions for role: [roleId]"
-   "Response status: [status]"
-   "Permissions data received: [data]"
-   "Rendering permissions for role: [role]"
-   "Tables to render: [tables]"

These help debug any issues.

## Success Indicators

✅ No errors about missing tables in Laravel database
✅ Schema tab shows tables from external database
✅ Users/Roles/Permissions saved to external database
✅ Data persists after page reload
✅ Multiple apps can have different data in their databases

## Next Steps

1. Test with multiple apps to verify isolation
2. Test with different database types (MySQL, PostgreSQL)
3. Verify all CRUD operations work correctly
4. Test pagination on schema tables
5. Test search functionality

## Need Help?

Check the browser console (F12) for errors and debug messages. They will show exactly what's happening.
