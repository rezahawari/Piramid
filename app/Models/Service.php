<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'cover_image_url',
        'is_active',
        'has_sohibul',
        'has_cooking_option',
        'default_cooking_option',
        'cooking_fee_idr',
        'cooking_fee_usd',
        'cooking_fee_cny',
        'cooking_fee_sar',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'has_sohibul' => 'boolean',
            'has_cooking_option' => 'boolean',
            'cooking_fee_idr' => 'decimal:2',
            'cooking_fee_usd' => 'decimal:2',
            'cooking_fee_cny' => 'decimal:2',
            'cooking_fee_sar' => 'decimal:2',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'service_product')->withTimestamps();
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
