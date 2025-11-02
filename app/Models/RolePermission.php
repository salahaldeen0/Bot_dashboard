<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $fillable = [
        'role_id',
        'permission',
        'actions',
    ];

    protected $casts = [
        'actions' => 'array',
    ];

    public function role()
    {
        return $this->belongsTo(AppRole::class, 'role_id');
    }
}
