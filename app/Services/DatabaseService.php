<?php

namespace App\Services;

use App\Models\App;
use App\Models\SchemaTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
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
     * Create schema_table in the external database
     */
    public function createSchemaTable(App $app): void
    {
        $pdo = $this->createPDOConnection($app);
        $driver = $this->mapDatabaseType($app->database_type);

        $sql = $this->getCreateTableSQL($driver);
        
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            // Table might already exist, which is fine
            if (strpos($e->getMessage(), 'already exists') === false) {
                throw new Exception("Failed to create schema_table: " . $e->getMessage());
            }
        }
    }

    /**
     * Get CREATE TABLE SQL based on database type
     */
    private function getCreateTableSQL(string $driver): string
    {
        switch ($driver) {
            case 'mysql':
                return "CREATE TABLE IF NOT EXISTS schema_table (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    table_name VARCHAR(255) NOT NULL UNIQUE,
                    keywords TEXT,
                    active_flag BOOLEAN DEFAULT TRUE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
            
            case 'pgsql':
                return "CREATE TABLE IF NOT EXISTS schema_table (
                    id SERIAL PRIMARY KEY,
                    table_name VARCHAR(255) NOT NULL UNIQUE,
                    keywords TEXT,
                    active_flag BOOLEAN DEFAULT TRUE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
            
            case 'sqlite':
                return "CREATE TABLE IF NOT EXISTS schema_table (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    table_name TEXT NOT NULL UNIQUE,
                    keywords TEXT,
                    active_flag INTEGER DEFAULT 1,
                    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
                )";
            
            case 'sqlsrv':
                return "IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='schema_table' AND xtype='U')
                    CREATE TABLE schema_table (
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
     * Sync tables from external database to schema_tables
     */
    public function syncTables(App $app): void
    {
        $externalTables = $this->fetchTables($app);
        
        foreach ($externalTables as $tableName) {
            // Skip the schema_table itself
            if ($tableName === 'schema_table') {
                continue;
            }

            SchemaTable::firstOrCreate(
                [
                    'app_id' => $app->id,
                    'table_name' => $tableName,
                ],
                [
                    'keywords' => '',
                    'active_flag' => true,
                ]
            );
        }

        // Remove tables that no longer exist in external database
        $app->schemaTables()
            ->whereNotIn('table_name', array_merge($externalTables, ['schema_table']))
            ->delete();
    }

    /**
     * Update table data in external schema_table
     */
    public function updateExternalSchemaTable(App $app, string $tableName, ?string $keywords, bool $activeFlag): void
    {
        $pdo = $this->createPDOConnection($app);
        $driver = $this->mapDatabaseType($app->database_type);

        // Check if record exists
        $checkSql = "SELECT COUNT(*) FROM schema_table WHERE table_name = :table_name";
        $stmt = $pdo->prepare($checkSql);
        $stmt->execute(['table_name' => $tableName]);
        $exists = $stmt->fetchColumn() > 0;

        if ($exists) {
            // Update existing record
            $sql = "UPDATE schema_table SET keywords = :keywords, active_flag = :active_flag, updated_at = " . 
                   $this->getCurrentTimestampSQL($driver) . " WHERE table_name = :table_name";
        } else {
            // Insert new record
            $sql = "INSERT INTO schema_table (table_name, keywords, active_flag) VALUES (:table_name, :keywords, :active_flag)";
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
}
