<?php

namespace App\Models;

use App\Enums\DistributionType;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'user_id',
        'service_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'unit_price',
        'cooking_option',
        'cooking_fee',
        'distribution_option_id',
        'distribution_fee',
        'currency',
        'total_amount',
        'distribution_type',
        'distribution_location_note',
        'recipient_name',
        'recipient_phone',
        'recipient_province',
        'recipient_city',
        'recipient_district',
        'recipient_address',
        'sohibul_names',
        'payment_method',
        'payment_status',
        'status',
        'manual_transfer_proof_url',
        'rejected_reason',
        'approved_by',
        'approved_at',
        'midtrans_order_id',
        'midtrans_snap_token',
        'midtrans_transaction_id',
        'midtrans_payment_type',
        'midtrans_va_number',
        'midtrans_transaction_status',
        'midtrans_fraud_status',
        'midtrans_settlement_time',
        'midtrans_raw_response',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'cooking_fee' => 'decimal:2',
            'distribution_fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'sohibul_names' => 'array',
            'payment_method' => PaymentMethod::class,
            'payment_status' => PaymentStatus::class,
            'status' => TransactionStatus::class,
            'approved_at' => 'datetime',
            'midtrans_settlement_time' => 'datetime',
            'midtrans_raw_response' => 'array',
        ];
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function distributionOption(): BelongsTo
    {
        return $this->belongsTo(DistributionOption::class, 'distribution_option_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function documentations(): HasMany
    {
        return $this->hasMany(TransactionDocumentation::class);
    }

    public function getRouteKeyName(): string
    {
        return 'transaction_code';
    }

    public static function generateCode(string $servicePrefix): string
    {
        return strtoupper($servicePrefix).'-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
    }
}
