<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Legality extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'document_number',
        'issuer',
        'issued_date',
        'file_path',
        'file_type',
        'file_size',
        'description',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'issued_date' => 'date',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
