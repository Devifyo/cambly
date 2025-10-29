<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'credits_per_cycle',
        'price',
        'stripe_product_id',
        'stripe_price_id',
        'subtitle',
        'description',
        'features',
        'is_popular',
        'icon_path',
        'slug',
        'interval',
        'status'
    ];

    protected $casts = [
        'features' => 'array',
    ];

        public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
