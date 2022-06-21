<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'sku', 'description'
    ];

    public function product_varient(){
        return $this->hasMany(ProductVarient::class);
    }

    public function product_varient_prices(){
        return $this->hasMany(ProductVariantPrice::class);
    }

    public function product_images(){
        return $this->hasMany(ProductImage::class);
    }

}
