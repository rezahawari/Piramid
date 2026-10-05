<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCheckoutRequest;
use App\Models\DistributionOption;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    /**
     * Ambil data opsi distribusi & metode pembayaran untuk form checkout.
     */
    public function options(Request $request): JsonResponse
    {
        $locale = $request->get('lang', 'id');

        $distributions = DistributionOption::active()
            ->get()
            ->map(fn (DistributionOption $dist) => [
                'id' => $dist->id,
                'name' => $dist->getLocalizedName($locale),
                'description' => $dist->getLocalizedDescription($locale),
                'fee' => [
                    'idr' => (float) $dist->fee_idr,
                    'usd' => (float) $dist->fee_usd,
                    'cny' => (float) $dist->fee_cny,
                    'sar' => (float) $dist->fee_sar,
                ],
            ]);

        return response()->json([
            'distribution_options' => $distributions,
            'payment_options' => collect(PaymentMethod::cases())
                ->map(fn (PaymentMethod $method) => ['value' => $method->value, 'label' => $method->label()])
                ->values(),
        ]);
    }

    /**
     * Simpan pesanan secara atomik dengan kalkulasi multi-currency, varian, dan cooking fee.
     */
    public function store(StoreCheckoutRequest $request): JsonResponse
    {
        $data = $request->validated();

        $service = Service::findOrFail($data['service_id']);
        $product = Product::findOrFail($data['product_id']);
        $distribution = DistributionOption::findOrFail($data['distribution_option_id']);
        $variant = !empty($data['product_variant_id']) ? ProductVariant::findOrFail($data['product_variant_id']) : null;

        $quantity = (int) $data['quantity'];
        $currency = strtoupper($data['currency'] ?? 'IDR');
        if (!in_array($currency, ['IDR', 'USD', 'CNY', 'SAR'])) {
            $currency = 'IDR';
        }
        $currencyKey = strtolower($currency);

        // 1. Tentukan Unit Price berdasarkan Varian / Produk & Currency
        if ($variant) {
            $priceField = 'price_' . $currencyKey;
            $unitPrice = (float) ($variant->{$priceField} ?? $variant->price_idr);
        } else {
            $priceField = 'price_' . $currencyKey;
            $unitPrice = (float) ($product->{$priceField} ?? $product->price);
        }

        // 2. Hitung Cooking Fee
        // Logika: Jika layanan mengaktifkan opsi masak, dan pilihan user BUKAN default, maka dikenakan biaya.
        $cookingOption = $data['cooking_option'] ?? ($service->has_cooking_option ? $service->default_cooking_option : null);
        $cookingFeePerUnit = 0;

        if ($service->has_cooking_option && $cookingOption && $cookingOption !== $service->default_cooking_option) {
            $feeField = 'cooking_fee_' . $currencyKey;
            $cookingFeePerUnit = (float) ($service->{$feeField} ?? $service->cooking_fee_idr);
        }

        // 3. Hitung Distribution Fee
        $distFeeField = 'fee_' . $currencyKey;
        $distributionFeePerUnit = (float) ($distribution->{$distFeeField} ?? $distribution->fee_idr);

        // 4. Total Amount
        $totalAmount = $quantity * ($unitPrice + $cookingFeePerUnit + $distributionFeePerUnit);

        $transaction = DB::transaction(function () use (
            $request,
            $data,
            $service,
            $product,
            $variant,
            $distribution,
            $quantity,
            $unitPrice,
            $cookingOption,
            $cookingFeePerUnit,
            $distributionFeePerUnit,
            $currency,
            $totalAmount
        ) {
            // Guard anti-oversell
            if ($variant) {
                $affected = ProductVariant::whereKey($variant->id)
                    ->where('stock', '>=', $quantity)
                    ->decrement('stock', $quantity);

                if ($affected === 0) {
                    throw ValidationException::withMessages([
                        'quantity' => ['Stok varian tidak mencukupi.'],
                    ]);
                }
            } else {
                $affected = Product::whereKey($product->id)
                    ->where('stock', '>=', $quantity)
                    ->decrement('stock', $quantity);

                if ($affected === 0) {
                    throw ValidationException::withMessages([
                        'quantity' => ['Stok produk tidak mencukupi.'],
                    ]);
                }
            }

            return Transaction::create([
                'transaction_code' => Transaction::generateCode(Str::substr($service->name, 0, 3)),
                'user_id' => $request->user()->id,
                'service_id' => $service->id,
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'cooking_option' => $cookingOption,
                'cooking_fee' => $cookingFeePerUnit * $quantity,
                'distribution_option_id' => $distribution->id,
                'distribution_fee' => $distributionFeePerUnit * $quantity,
                'currency' => $currency,
                'total_amount' => $totalAmount,
                'distribution_type' => null,
                'distribution_location_note' => $data['distribution_location_note'] ?? null,
                'sohibul_names' => ! empty($data['sohibul_names']) ? array_values(array_filter($data['sohibul_names'])) : null,
                'payment_method' => $data['payment_method'],
                'payment_status' => PaymentStatus::Pending,
                'status' => TransactionStatus::Menunggu,
            ]);
        });

        return response()->json([
            'message' => 'Pesanan berhasil dibuat. Silakan selesaikan pembayaran.',
            'transaction' => [
                'id' => $transaction->id,
                'transaction_code' => $transaction->transaction_code,
                'currency' => $transaction->currency,
                'unit_price' => (float) $transaction->unit_price,
                'cooking_fee' => (float) $transaction->cooking_fee,
                'distribution_fee' => (float) $transaction->distribution_fee,
                'total_amount' => (float) $transaction->total_amount,
                'status' => $transaction->status->value,
                'status_label' => $transaction->status->label(),
                'payment_status' => $transaction->payment_status->value,
                'payment_status_label' => $transaction->payment_status->label(),
                'payment_method' => $transaction->payment_method->value,
                'payment_method_label' => $transaction->payment_method->label(),
                'created_at' => $transaction->created_at,
            ],
        ], 201);
    }
}
