<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        // 'image',
        'description',
        'price',
        'stock',
        'category_id',
        'depot_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];



    // public function getImageUrlAttribute()
    // {
    //     return $this->image
    //         ? asset('storage/' . $this->image)
    //         : asset('images/logo.png');
    // }



    public function category(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    public function depot(): BelongsTo
    {
        return $this->belongsTo(Depot::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }


    public function cartItems(): HasMany
{
    return $this->hasMany(CartItem::class);
}

}
