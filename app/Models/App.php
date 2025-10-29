<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class App extends Model
{
    protected $fillable = [
        'app_name',
        'description',
        'phone_number',
        'database_type',
        'database_name',
        'port',
        'host',
        'username',
        'password',
        'is_connected',
        'has_synced_schema',
        'users_count',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'port' => 'integer',
        'is_connected' => 'boolean',
        'has_synced_schema' => 'boolean',
        'users_count' => 'integer',
    ];

    /**
     * Get the decrypted password
     */
    public function getPasswordAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // If decryption fails, return the original value
            // This handles old hashed passwords during migration
            return $value;
        }
    }

    public function schemaTables()
    {
        return $this->hasMany(SchemaTable::class);
    }

    /**
     * Check if Schema tab should be visible
     * Schema tab shows after successful database connection
     */
    public function canShowSchemaTab()
    {
        return $this->is_connected;
    }

    /**
     * Check if Users tab should be visible
     * Users tab shows after schema has been synced
     */
    public function canShowUsersTab()
    {
        return $this->is_connected && $this->has_synced_schema;
    }

    /**
     * Check if Roles & Permissions tab should be visible
     * Roles & Permissions tab shows after at least one user is created
     */
    public function canShowRolesTab()
    {
        return $this->is_connected && $this->has_synced_schema && $this->users_count > 0;
    }
}