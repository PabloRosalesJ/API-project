<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $casts = [
        'created_at' => 'date:d-m-Y H:i:s',
        'updated_at' => 'date:d-m-Y H:i:s',
        'deleted_at' => 'date:d-m-Y H:i:s',
    ];

    protected $hidden = [
        'deleted_at',
    ];
}
