<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DistributionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_id',
        'name_en',
        'name_zh',
        'name_ar',
        'description_id',
        'description_en',
        'description_zh',
        'description_ar',
        'fee_idr',
        'fee_usd',
        'fee_cny',
        'fee_sar',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'fee_idr' => 'decimal:2',
            'fee_usd' => 'decimal:2',
            'fee_cny' => 'decimal:2',
            'fee_sar' => 'decimal:2',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    /**
     * Get localized name based on given locale code.
     */
    public function getLocalizedName(string $locale = 'id'): string
    {
        $field = 'name_' . $locale;
        return $this->{$field} ?: $this->name_id;
    }

    /**
     * Get localized description based on given locale code.
     */
    public function getLocalizedDescription(string $locale = 'id'): ?string
    {
        $field = 'description_' . $locale;
        return $this->{$field} ?: $this->description_id;
    }
}
