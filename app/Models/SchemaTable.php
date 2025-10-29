<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchemaTable extends Model
{
    protected $fillable = [
        'app_id',
        'table_name',
        'keywords',
        'active_flag',
    ];

    protected $casts = [
        'active_flag' => 'boolean',
    ];

    public function app()
    {
        return $this->belongsTo(App::class);
    }
}
