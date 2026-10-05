<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'price_usd',
        'price_cny',
        'price_sar',
        'weight_estimate_kg',
        'stock',
        'max_sohibul',
        'primary_image_url',
        'gallery',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'price_usd' => 'decimal:2',
            'price_cny' => 'decimal:2',
            'price_sar' => 'decimal:2',
            'weight_estimate_kg' => 'decimal:2',
            'max_sohibul' => 'integer',
            'gallery' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('order');
    }

    public function activeVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true)->orderBy('order');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_product')->withTimestamps();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
