<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppUser extends Model
{
    protected $fillable = [
        'app_id',
        'name',
        'phone',
    ];

    public function app()
    {
        return $this->belongsTo(App::class);
    }
}
