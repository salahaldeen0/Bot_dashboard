<?php

namespace App\Services;

use App\Models\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use PDO;
use PDOException;
use Exception;

class DatabaseService
{
    /**
     * Test database connection
     */
    public function testConnection(App $app): bool
    {
        try {
            $pdo = $this->createPDOConnection($app);
            $pdo = null; // Close connection
            return true;
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Create PDO connection to external database
     */
    private function createPDOConnection(App $app): PDO
    {
        $driver = $this->mapDatabaseType($app->database_type);
        
        if ($driver === 'sqlite') {
            $dsn = "sqlite:" . $app->database_name;
            return new PDO($dsn);
        }

        $dsn = $this->buildDSN($driver, $app);
        return new PDO($dsn, $app->username, $app->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    /**
     * Build DSN string based on database type
     */
    private function buildDSN(string $driver, App $app): string
    {
        switch ($driver) {
            case 'mysql':
                return "mysql:host={$app->host};port={$app->port};dbname={$app->database_name};charset=utf8mb4";
            case 'pgsql':
                return "pgsql:host={$app->host};port={$app->port};dbname={$app->database_name}";
            case 'sqlsrv':
                return "sqlsrv:Server={$app->host},{$app->port};Database={$app->database_name}";
            default:
                throw new Exception("Unsupported database type: {$driver}");
        }
    }

    /**
     * Map database type to PDO driver
     */
    private function mapDatabaseType(string $type): string
    {
        $map = [
            'mysql' => 'mysql',
            'postgresql' => 'pgsql',
            'sqlite' => 'sqlite',
            'sqlserver' => 'sqlsrv',
            'oracle' => 'oci',
        ];

        return $map[$type] ?? $type;
    }

    /**
     * Create ai_bot_schema_table in the external database
     */
    public function createSchemaTable(App $app): void
    {
        $pdo = $this->createPDOConnection($app);
        $driver = $this->mapDatabaseType($app->database_type);

        $sql = $this->getCreateSchemaTableSQL($driver);
        
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            // Table might already exist, which is fine
            if (strpos($e->getMessage(), 'already exists') === false) {
                throw new Exception("Failed to create ai_bot_schema_table: " . $e->getMessage());
            }
        }
    }

    /**
     * Create all AI bot tables in the external database
     */
    public function createAllBotTables(App $app): void
    {
        // Create schema table first
        $this->createSchemaTable($app);
        
        // Create roles, users, and permissions tables
        $this->createRolesTable($app);
        $this->createUsersTable($app);
        $this->createPermissionsTable($app);
    }

    /**
     * Create ai_bot_roles table in the external database
     */
    public function createRolesTable(App $app): void
    {
        $pdo = $this->createPDOConnection($app);
        $driver = $this->mapDatabaseType($app->database_type);

        $sql = $this->getCreateRolesTableSQL($driver);
        
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'already exists') === false) {
                throw new Exception("Failed to create ai_bot_roles table: " . $e->getMessage());
            }
        }
    }

    /**
     * Create ai_bot_users table in the external database
     */
    public function createUsersTable(App $app): void
    {
        $pdo = $this->createPDOConnection($app);
        $driver = $this->mapDatabaseType($app->database_type);

        $sql = $this->getCreateUsersTableSQL($driver);
        
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'already exists') === false) {
                throw new Exception("Failed to create ai_bot_users table: " . $e->getMessage());
            }
        }
    }

    /**
     * Create ai_bot_permissions table in the external database
     */
    public function createPermissionsTable(App $app): void
    {
        $pdo = $this->createPDOConnection($app);
        $driver = $this->mapDatabaseType($app->database_type);

        $sql = $this->getCreatePermissionsTableSQL($driver);
        
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'already exists') === false) {
                throw new Exception("Failed to create ai_bot_permissions table: " . $e->getMessage());
            }
        }
    }

    /**
     * Get CREATE TABLE SQL for ai_bot_schema_table based on database type
     */
    private function getCreateSchemaTableSQL(string $driver): string
    {
        switch ($driver) {
            case 'mysql':
                return "CREATE TABLE IF NOT EXISTS ai_bot_schema_table (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    table_name VARCHAR(255) NOT NULL UNIQUE,
                    keywords TEXT,
                    active_flag BOOLEAN DEFAULT TRUE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
            
            case 'pgsql':
                return "CREATE TABLE IF NOT EXISTS ai_bot_schema_table (
                    id SERIAL PRIMARY KEY,
                    table_name VARCHAR(255) NOT NULL UNIQUE,
                    keywords TEXT,
                    active_flag BOOLEAN DEFAULT TRUE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
            
            case 'sqlite':
                return "CREATE TABLE IF NOT EXISTS ai_bot_schema_table (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    table_name TEXT NOT NULL UNIQUE,
                    keywords TEXT,
                    active_flag INTEGER DEFAULT 1,
                    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
                )";
            
            case 'sqlsrv':
                return "IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='ai_bot_schema_table' AND xtype='U')
                    CREATE TABLE ai_bot_schema_table (
                        id INT IDENTITY(1,1) PRIMARY KEY,
                        table_name VARCHAR(255) NOT NULL UNIQUE,
                        keywords TEXT,
                        active_flag BIT DEFAULT 1,
                        created_at DATETIME DEFAULT GETDATE(),
                        updated_at DATETIME DEFAULT GETDATE()
                    )";
            
            default:
                throw new Exception("Unsupported database type for table creation");
        }
    }

    /**
     * Get CREATE TABLE SQL for ai_bot_roles based on database type
     */
    private function getCreateRolesTableSQL(string $driver): string
    {
        switch ($driver) {
            case 'mysql':
                return "CREATE TABLE IF NOT EXISTS ai_bot_roles (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    app_id INT NOT NULL,
                    role_name VARCHAR(255) NOT NULL,
                    description TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
            
            case 'pgsql':
                return "CREATE TABLE IF NOT EXISTS ai_bot_roles (
                    id SERIAL PRIMARY KEY,
                    app_id INT NOT NULL,
                    role_name VARCHAR(255) NOT NULL,
                    description TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
            
            case 'sqlite':
                return "CREATE TABLE IF NOT EXISTS ai_bot_roles (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    app_id INTEGER NOT NULL,
                    role_name TEXT NOT NULL,
                    description TEXT,
                    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
                )";
            
            case 'sqlsrv':
                return "IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='ai_bot_roles' AND xtype='U')
                    CREATE TABLE ai_bot_roles (
                        id INT IDENTITY(1,1) PRIMARY KEY,
                        app_id INT NOT NULL,
                        role_name VARCHAR(255) NOT NULL,
                        description TEXT,
                        created_at DATETIME DEFAULT GETDATE(),
                        updated_at DATETIME DEFAULT GETDATE()
                    )";
            
            default:
                throw new Exception("Unsupported database type for table creation");
        }
    }

    /**
     * Get CREATE TABLE SQL for ai_bot_users based on database type
     */
    private function getCreateUsersTableSQL(string $driver): string
    {
        switch ($driver) {
            case 'mysql':
                return "CREATE TABLE IF NOT EXISTS ai_bot_users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    app_id INT NOT NULL,
                    name VARCHAR(255) NOT NULL,
                    phone VARCHAR(50),
                    role_id INT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (role_id) REFERENCES ai_bot_roles(id) ON DELETE SET NULL
                )";
            
            case 'pgsql':
                return "CREATE TABLE IF NOT EXISTS ai_bot_users (
                    id SERIAL PRIMARY KEY,
                    app_id INT NOT NULL,
                    name VARCHAR(255) NOT NULL,
                    phone VARCHAR(50),
                    role_id INT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (role_id) REFERENCES ai_bot_roles(id) ON DELETE SET NULL
                )";
            
            case 'sqlite':
                return "CREATE TABLE IF NOT EXISTS ai_bot_users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    app_id INTEGER NOT NULL,
                    name TEXT NOT NULL,
                    phone TEXT,
                    role_id INTEGER,
                    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                    updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (role_id) REFERENCES ai_bot_roles(id) ON DELETE SET NULL
                )";
            
            case 'sqlsrv':
                return "IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='ai_bot_users' AND xtype='U')
                    CREATE TABLE ai_bot_users (
                        id INT IDENTITY(1,1) PRIMARY KEY,
                        app_id INT NOT NULL,
                        name VARCHAR(255) NOT NULL,
                        phone VARCHAR(50),
                        role_id INT,
                        created_at DATETIME DEFAULT GETDATE(),
                        updated_at DATETIME DEFAULT GETDATE(),
                        FOREIGN KEY (role_id) REFERENCES ai_bot_roles(id) ON DELETE SET NULL
                    )";
            
            default:
                throw new Exception("Unsupported database type for table creation");
        }
    }

    /**
     * Get CREATE TABLE SQL for ai_bot_permissions based on database type
     */
    private function getCreatePermissionsTableSQL(string $driver): string
    {
        switch ($driver) {
            case 'mysql':
                return "CREATE TABLE IF NOT EXISTS ai_bot_permissions (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    app_id INT NOT NULL,
                    role_id INT NOT NULL,
                    permission VARCHAR(255) NOT NULL,
                    actions TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (role_id) REFERENCES ai_bot_roles(id) ON DELETE CASCADE
                )";
            
            case 'pgsql':
                return "CREATE TABLE IF NOT EXISTS ai_bot_permissions (
                    id SERIAL PRIMARY KEY,
                    app_id INT NOT NULL,
                    role_id INT NOT NULL,
                    permission VARCHAR(255) NOT NULL,
                    actions TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (role_id) REFERENCES ai_bot_roles(id) ON DELETE CASCADE
                )";
            
            case 'sqlite':
                return "CREATE TABLE IF NOT EXISTS ai_bot_permissions (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    app_id INTEGER NOT NULL,
                    role_id INTEGER NOT NULL,
                    permission TEXT NOT NULL,
                    actions TEXT,
                    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                    updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (role_id) REFERENCES ai_bot_roles(id) ON DELETE CASCADE
                )";
            
            case 'sqlsrv':
                return "IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='ai_bot_permissions' AND xtype='U')
                    CREATE TABLE ai_bot_permissions (
                        id INT IDENTITY(1,1) PRIMARY KEY,
                        app_id INT NOT NULL,
                        role_id INT NOT NULL,
                        permission VARCHAR(255) NOT NULL,
                        actions TEXT,
                        created_at DATETIME DEFAULT GETDATE(),
                        updated_at DATETIME DEFAULT GETDATE(),
                        FOREIGN KEY (role_id) REFERENCES ai_bot_roles(id) ON DELETE CASCADE
                    )";
            
            default:
                throw new Exception("Unsupported database type for table creation");
        }
    }

    /**
     * Fetch all tables from the external database
     */
    public function fetchTables(App $app): array
    {
        $pdo = $this->createPDOConnection($app);
        $driver = $this->mapDatabaseType($app->database_type);

        $sql = $this->getShowTablesSQL($driver, $app->database_name);
        $stmt = $pdo->query($sql);
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get SQL to show all tables based on database type
     */
    private function getShowTablesSQL(string $driver, string $dbName): string
    {
        switch ($driver) {
            case 'mysql':
                return "SHOW TABLES";
            
            case 'pgsql':
                return "SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname != 'pg_catalog' AND schemaname != 'information_schema'";
            
            case 'sqlite':
                return "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'";
            
            case 'sqlsrv':
                return "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'";
            
            default:
                throw new Exception("Unsupported database type for fetching tables");
        }
    }

    /**
     * Sync tables from external database to ai_bot_schema_table (in external database only)
     */
    public function syncTables(App $app): void
    {
        $externalTables = $this->fetchTables($app);
        
        foreach ($externalTables as $tableName) {
            // Skip AI bot tables
            if ($this->isAIBotTable($tableName)) {
                continue;
            }

            // Save to the external database's ai_bot_schema_table
            $this->syncTableToExternalDatabase($app, $tableName, '', true);
        }

        // Remove tables that no longer exist in external database
        $aiBotTables = $this->getAIBotTableNames();
        $excludedTables = array_merge($externalTables, $aiBotTables);
        
        // Get existing tables from external database
        $pdo = $this->createPDOConnection($app);
        $sql = "SELECT table_name FROM ai_bot_schema_table";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Delete tables that no longer exist
        foreach ($existingTables as $existingTable) {
            if (!in_array($existingTable, $excludedTables)) {
                $this->deleteTableFromExternalDatabase($app, $existingTable);
            }
        }
    }

    /**
     * Check if table is an AI bot table
     */
    private function isAIBotTable(string $tableName): bool
    {
        return strpos($tableName, 'ai_bot_') === 0;
    }

    /**
     * Get list of AI bot table names
     */
    private function getAIBotTableNames(): array
    {
        return [
            'ai_bot_schema_table',
            'ai_bot_roles',
            'ai_bot_users',
            'ai_bot_permissions',
        ];
    }

    /**
     * Sync a table to the external database's ai_bot_schema_table
     */
    private function syncTableToExternalDatabase(App $app, string $tableName, ?string $keywords, bool $activeFlag): void
    {
        $pdo = $this->createPDOConnection($app);
        $driver = $this->mapDatabaseType($app->database_type);

        // Check if record exists
        $checkSql = "SELECT COUNT(*) FROM ai_bot_schema_table WHERE table_name = :table_name";
        $stmt = $pdo->prepare($checkSql);
        $stmt->execute(['table_name' => $tableName]);
        $exists = $stmt->fetchColumn() > 0;

        if ($exists) {
            // Update existing record
            $sql = "UPDATE ai_bot_schema_table SET keywords = :keywords, active_flag = :active_flag, updated_at = " . 
                   $this->getCurrentTimestampSQL($driver) . " WHERE table_name = :table_name";
        } else {
            // Insert new record
            $sql = "INSERT INTO ai_bot_schema_table (table_name, keywords, active_flag) VALUES (:table_name, :keywords, :active_flag)";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'table_name' => $tableName,
            'keywords' => $keywords ?? '',
            'active_flag' => $activeFlag ? 1 : 0,
        ]);
    }

    /**
     * Delete a table from the external database's ai_bot_schema_table
     */
    private function deleteTableFromExternalDatabase(App $app, string $tableName): void
    {
        try {
            $pdo = $this->createPDOConnection($app);
            
            $sql = "DELETE FROM ai_bot_schema_table WHERE table_name = :table_name";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['table_name' => $tableName]);
        } catch (PDOException $e) {
            // Log the error but don't fail the entire sync process
            // The table might not exist in the external database
            Log::warning("Failed to delete table {$tableName} from external ai_bot_schema_table: " . $e->getMessage());
        }
    }

    /**
     * Update table data in external ai_bot_schema_table
     */
    public function updateExternalSchemaTable(App $app, string $tableName, ?string $keywords, bool $activeFlag): void
    {
        $pdo = $this->createPDOConnection($app);
        $driver = $this->mapDatabaseType($app->database_type);

        // Check if record exists
        $checkSql = "SELECT COUNT(*) FROM ai_bot_schema_table WHERE table_name = :table_name";
        $stmt = $pdo->prepare($checkSql);
        $stmt->execute(['table_name' => $tableName]);
        $exists = $stmt->fetchColumn() > 0;

        if ($exists) {
            // Update existing record
            $sql = "UPDATE ai_bot_schema_table SET keywords = :keywords, active_flag = :active_flag, updated_at = " . 
                   $this->getCurrentTimestampSQL($driver) . " WHERE table_name = :table_name";
        } else {
            // Insert new record
            $sql = "INSERT INTO ai_bot_schema_table (table_name, keywords, active_flag) VALUES (:table_name, :keywords, :active_flag)";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'table_name' => $tableName,
            'keywords' => $keywords ?? '',
            'active_flag' => $activeFlag ? 1 : 0,
        ]);
    }

    /**
     * Get current timestamp SQL based on database type
     */
    private function getCurrentTimestampSQL(string $driver): string
    {
        switch ($driver) {
            case 'mysql':
                return 'CURRENT_TIMESTAMP';
            case 'pgsql':
                return 'CURRENT_TIMESTAMP';
            case 'sqlite':
                return "datetime('now')";
            case 'sqlsrv':
                return 'GETDATE()';
            default:
                return 'CURRENT_TIMESTAMP';
        }
    }

    /**
     * Insert a role into the external ai_bot_roles table
     */
    public function insertRole(App $app, int $appId, string $roleName, ?string $description = null): int
    {
        $pdo = $this->createPDOConnection($app);
        
        $sql = "INSERT INTO ai_bot_roles (app_id, role_name, description) VALUES (:app_id, :role_name, :description)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'app_id' => $appId,
            'role_name' => $roleName,
            'description' => $description,
        ]);
        
        return (int) $pdo->lastInsertId();
    }

    /**
     * Insert a user into the external ai_bot_users table
     */
    public function insertUser(App $app, int $appId, string $name, ?string $phone = null, ?int $roleId = null): int
    {
        $pdo = $this->createPDOConnection($app);
        
        $sql = "INSERT INTO ai_bot_users (app_id, name, phone, role_id) VALUES (:app_id, :name, :phone, :role_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'app_id' => $appId,
            'name' => $name,
            'phone' => $phone,
            'role_id' => $roleId,
        ]);
        
        return (int) $pdo->lastInsertId();
    }

    /**
     * Insert a permission into the external ai_bot_permissions table
     */
    public function insertPermission(App $app, int $appId, int $roleId, string $permission, ?string $actions = null): int
    {
        $pdo = $this->createPDOConnection($app);
        
        $sql = "INSERT INTO ai_bot_permissions (app_id, role_id, permission, actions) VALUES (:app_id, :role_id, :permission, :actions)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'app_id' => $appId,
            'role_id' => $roleId,
            'permission' => $permission,
            'actions' => $actions,
        ]);
        
        return (int) $pdo->lastInsertId();
    }

    /**
     * Get all roles from the external ai_bot_roles table
     */
    public function getRoles(App $app, int $appId): array
    {
        $pdo = $this->createPDOConnection($app);
        
        $sql = "SELECT * FROM ai_bot_roles WHERE app_id = :app_id ORDER BY role_name";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['app_id' => $appId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all users from the external ai_bot_users table
     */
    public function getUsers(App $app, int $appId): array
    {
        $pdo = $this->createPDOConnection($app);
        
        $sql = "SELECT u.*, r.role_name 
                FROM ai_bot_users u 
                LEFT JOIN ai_bot_roles r ON u.role_id = r.id 
                WHERE u.app_id = :app_id 
                ORDER BY u.name";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['app_id' => $appId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all permissions for a role from the external ai_bot_permissions table
     */
    public function getPermissionsByRole(App $app, int $roleId): array
    {
        $pdo = $this->createPDOConnection($app);
        
        $sql = "SELECT * FROM ai_bot_permissions WHERE role_id = :role_id ORDER BY permission";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['role_id' => $roleId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if AI bot tables exist in the connected database
     */
    public function checkBotTablesExist(App $app): array
    {
        $tables = $this->fetchTables($app);
        $botTables = $this->getAIBotTableNames();
        
        $status = [];
        foreach ($botTables as $tableName) {
            $status[$tableName] = in_array($tableName, $tables);
        }
        
        return $status;
    }

    /**
     * Get count of records in AI bot tables
     */
    public function getBotTableStats(App $app): array
    {
        $pdo = $this->createPDOConnection($app);
        $stats = [];
        
        try {
            // Count roles
            $stmt = $pdo->query("SELECT COUNT(*) FROM ai_bot_roles");
            $stats['roles_count'] = (int) $stmt->fetchColumn();
            
            // Count users
            $stmt = $pdo->query("SELECT COUNT(*) FROM ai_bot_users");
            $stats['users_count'] = (int) $stmt->fetchColumn();
            
            // Count permissions
            $stmt = $pdo->query("SELECT COUNT(*) FROM ai_bot_permissions");
            $stats['permissions_count'] = (int) $stmt->fetchColumn();
            
            // Count schema tables
            $stmt = $pdo->query("SELECT COUNT(*) FROM ai_bot_schema_table");
            $stats['schema_tables_count'] = (int) $stmt->fetchColumn();
            
        } catch (PDOException $e) {
            // Tables might not exist yet
            $stats['error'] = $e->getMessage();
        }
        
        return $stats;
    }

    /**
     * Update a user in the external ai_bot_users table
     */
    public function updateUser(App $app, int $userId, string $name, ?string $phone = null, ?int $roleId = null): void
    {
        $pdo = $this->createPDOConnection($app);
        
        $sql = "UPDATE ai_bot_users SET name = :name, phone = :phone, role_id = :role_id WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'phone' => $phone,
            'role_id' => $roleId,
            'id' => $userId,
        ]);
    }

    /**
     * Delete a user from the external ai_bot_users table
     */
    public function deleteUser(App $app, int $userId): void
    {
        $pdo = $this->createPDOConnection($app);
        
        $sql = "DELETE FROM ai_bot_users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
    }

    /**
     * Update a role in the external ai_bot_roles table
     */
    public function updateRole(App $app, int $roleId, string $roleName, ?string $description = null): void
    {
        $pdo = $this->createPDOConnection($app);
        
        $sql = "UPDATE ai_bot_roles SET role_name = :role_name, description = :description WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'role_name' => $roleName,
            'description' => $description,
            'id' => $roleId,
        ]);
    }

    /**
     * Delete a role from the external ai_bot_roles table
     */
    public function deleteRole(App $app, int $roleId): void
    {
        $pdo = $this->createPDOConnection($app);
        
        $sql = "DELETE FROM ai_bot_roles WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $roleId]);
    }

    /**
     * Get a single user from the external ai_bot_users table
     */
    public function getUser(App $app, int $userId): ?array
    {
        $pdo = $this->createPDOConnection($app);
        
        $sql = "SELECT u.*, r.role_name 
                FROM ai_bot_users u 
                LEFT JOIN ai_bot_roles r ON u.role_id = r.id 
                WHERE u.id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get a single role from the external ai_bot_roles table
     */
    public function getRole(App $app, int $roleId): ?array
    {
        $pdo = $this->createPDOConnection($app);
        
        $sql = "SELECT * FROM ai_bot_roles WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $roleId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get all schema tables from external database with pagination
     */
    public function getSchemaTables(App $app, int $page = 1, int $perPage = 10, string $search = ''): array
    {
        $pdo = $this->createPDOConnection($app);
        $driver = $this->mapDatabaseType($app->database_type);
        
        $offset = ($page - 1) * $perPage;
        
        // Build WHERE clause for search
        $whereClause = '';
        $params = [];
        if ($search) {
            $whereClause = ' WHERE table_name LIKE :search';
            $params['search'] = "%{$search}%";
        }
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM ai_bot_schema_table" . $whereClause;
        $stmt = $pdo->prepare($countSql);
        $stmt->execute($params);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Build pagination SQL based on database type
        if ($driver === 'sqlsrv') {
            // SQL Server uses OFFSET...FETCH syntax
            $sql = "SELECT * FROM ai_bot_schema_table" . $whereClause . " ORDER BY table_name OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";
        } else {
            // MySQL, PostgreSQL, SQLite use LIMIT...OFFSET syntax
            $sql = "SELECT * FROM ai_bot_schema_table" . $whereClause . " ORDER BY table_name LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
        ];
    }

    /**
     * Get a single schema table from external database
     */
    public function getSchemaTable(App $app, int $tableId): ?array
    {
        $pdo = $this->createPDOConnection($app);
        
        $sql = "SELECT * FROM ai_bot_schema_table WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $tableId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get permissions for a specific role from external database
     */
    public function getPermissionsForRole(App $app, int $roleId): array
    {
        $pdo = $this->createPDOConnection($app);
        
        // Get all active schema tables
        $tableSql = "SELECT table_name FROM ai_bot_schema_table WHERE active_flag = 1 ORDER BY table_name";
        $tableStmt = $pdo->prepare($tableSql);
        $tableStmt->execute();
        $tables = $tableStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get existing permissions for this role
        $permSql = "SELECT permission, actions FROM ai_bot_permissions WHERE role_id = :role_id";
        $permStmt = $pdo->prepare($permSql);
        $permStmt->execute(['role_id' => $roleId]);
        $permissions = $permStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Create a map of permissions
        $permissionsMap = [];
        foreach ($permissions as $permission) {
            $actions = $permission['actions'];
            // Handle JSON decoding if needed
            if (is_string($actions)) {
                $actions = json_decode($actions, true);
            }
            $permissionsMap[$permission['permission']] = $actions ?: [];
        }
        
        // Build response with all tables and their permission status
        $result = [];
        foreach ($tables as $table) {
            $result[] = [
                'table_name' => $table['table_name'],
                'actions' => $permissionsMap[$table['table_name']] ?? [],
            ];
        }
        
        return $result;
    }

    /**
     * Update permissions for a role in external database
     */
    public function updatePermissionsForRole(App $app, int $roleId, array $permissions): void
    {
        $pdo = $this->createPDOConnection($app);
        
        foreach ($permissions as $permissionData) {
            $tableName = $permissionData['table_name'];
            $actions = $permissionData['actions'] ?? [];
            
            if (empty($actions)) {
                // If no actions, delete the permission record
                $deleteSql = "DELETE FROM ai_bot_permissions WHERE role_id = :role_id AND permission = :permission";
                $stmt = $pdo->prepare($deleteSql);
                $stmt->execute([
                    'role_id' => $roleId,
                    'permission' => $tableName,
                ]);
            } else {
                // Check if record exists
                $checkSql = "SELECT COUNT(*) as count FROM ai_bot_permissions WHERE role_id = :role_id AND permission = :permission";
                $checkStmt = $pdo->prepare($checkSql);
                $checkStmt->execute([
                    'role_id' => $roleId,
                    'permission' => $tableName,
                ]);
                $exists = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
                
                $actionsJson = json_encode($actions);
                
                if ($exists) {
                    // Update existing record
                    $updateSql = "UPDATE ai_bot_permissions SET actions = :actions WHERE role_id = :role_id AND permission = :permission";
                    $stmt = $pdo->prepare($updateSql);
                    $stmt->execute([
                        'actions' => $actionsJson,
                        'role_id' => $roleId,
                        'permission' => $tableName,
                    ]);
                } else {
                    // Insert new record
                    $insertSql = "INSERT INTO ai_bot_permissions (app_id, role_id, permission, actions) VALUES (:app_id, :role_id, :permission, :actions)";
                    $stmt = $pdo->prepare($insertSql);
                    $stmt->execute([
                        'app_id' => $app->id,
                        'role_id' => $roleId,
                        'permission' => $tableName,
                        'actions' => $actionsJson,
                    ]);
                }
            }
        }
    }

    /**
     * Get count of schema tables in external database
     */
    public function getSchemaTableCount(App $app): int
    {
        try {
            $pdo = $this->createPDOConnection($app);
            $stmt = $pdo->query("SELECT COUNT(*) FROM ai_bot_schema_table");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            Log::warning("Failed to get schema table count: " . $e->getMessage());
            return 0;
        }
    }
}
