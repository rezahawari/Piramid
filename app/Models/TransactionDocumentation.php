<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionDocumentation extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'stage',
        'type',
        'file_url',
        'cloudinary_public_id',
        'caption',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'stage' => TransactionStatus::class,
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
