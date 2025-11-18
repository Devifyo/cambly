<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
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

        /**
     * Bootstrap model events to auto-generate slug when needed.
     */
    protected static function booted()
    {
        // Before creating: generate slug if not provided
        static::creating(function (Plan $plan) {
            if (empty($plan->slug)) {
                $plan->slug = static::createUniqueSlug($plan->name);
            }
        });

        // Before updating: if slug empty (or null) generate one from name
        static::updating(function (Plan $plan) {
            if (empty($plan->slug)) {
                $plan->slug = static::createUniqueSlug($plan->name, $plan->id);
            }
        });
    }

    /**
     * Create a unique slug based on the given name.
     *
     * @param  string|null  $name
     * @param  int|null     $ignoreId  Optional model id to ignore (useful on update)
     * @return string
     */
    protected static function createUniqueSlug(?string $name, ?int $ignoreId = null): string
    {
        // Fallback if name is empty
        $base = $name ? Str::slug($name) : Str::random(8);

        // Ensure we have something (slug could be empty if name had only unsupported chars)
        if (empty($base)) {
            $base = 'plan-' . time();
        }

        $slug = $base;
        $counter = 2;

        // Build initial query including soft-deleted models to avoid reusing slugs
        $query = static::withTrashed()->where('slug', $slug);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        // Loop until we find an unused slug
        while ($query->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;

            $query = static::withTrashed()->where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
        }

        return $slug;
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
