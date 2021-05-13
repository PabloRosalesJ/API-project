<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
