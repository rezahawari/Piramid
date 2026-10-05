<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name_id',
        'name_en',
        'name_zh',
        'name_ar',
        'spec_description',
        'price_idr',
        'price_usd',
        'price_cny',
        'price_sar',
        'stock',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_idr' => 'decimal:2',
            'price_usd' => 'decimal:2',
            'price_cny' => 'decimal:2',
            'price_sar' => 'decimal:2',
            'stock' => 'integer',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    public function getLocalizedName(string $locale = 'id'): string
    {
        $field = 'name_' . $locale;
        return $this->{$field} ?: $this->name_id;
    }
}
