<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Plan extends Model
{
    use HasFactory, SoftDeletes;
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
    public function getIconLinkAttribute()
    {
        // If a custom icon path exists
        if ($this->icon_path) {

            // If path already starts with "assets", return as public asset
            if (str_starts_with($this->icon_path, 'assets')) {
                return asset($this->icon_path);
            }

            // Otherwise return storage URL
            return Storage::url($this->icon_path);
        }

        // Default icon
        return asset('assets/img/icons/price-icon1.svg');
    }

}
