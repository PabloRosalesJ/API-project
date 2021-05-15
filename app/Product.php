<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\Types\This;

class Product extends BaseModel
{
    use SoftDeletes;

    const PRODUCTO_DISPONIBLE = 'disponible';
    const PRODUCTO_NO_DISPONIBLE = 'no disponible';

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'status',
        'image',
        'seller_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating( function ($product)
            {
                if ($product->quantity == 0 && $product->isAvailable()) {

                    $product->status = Product::PRODUCTO_NO_DISPONIBLE;
                    $product->save();

                }
            }
        );
    }

    public function isAvailable()
    {
        return $this->status == Product::PRODUCTO_DISPONIBLE;
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }
}
