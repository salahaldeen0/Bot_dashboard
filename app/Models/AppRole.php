<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppRole extends Model
{
    protected $fillable = [
        'app_id',
        'role_name',
        'description',
    ];

    public function app()
    {
        return $this->belongsTo(App::class);
    }

    public function permissions()
    {
        return $this->hasMany(RolePermission::class, 'role_id');
    }
}
