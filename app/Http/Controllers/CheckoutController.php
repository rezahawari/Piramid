<?php

namespace App\Http\Controllers;

use App\Enums\DistributionType;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Http\Requests\StoreCheckoutRequest;
use App\Models\Product;
use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    /**
     * USR-03 — Form checkout untuk satu produk dalam konteks layanan.
     */
    public function create(Service $service, Product $product): Response
    {
        abort_unless($service->is_active && $product->is_active, 404);

        return Inertia::render('Checkout/Create', [
            'service' => $service->only(['id', 'name', 'slug']),
            'product' => $product->only([
                'id',
                'name',
                'slug',
                'price',
                'weight_estimate_kg',
                'stock',
                'primary_image_url',
            ]),
            'distribution_options' => collect(DistributionType::cases())
                ->map(fn (DistributionType $type) => ['value' => $type->value, 'label' => $type->label()])
                ->values(),
            'payment_options' => collect(PaymentMethod::cases())
                ->map(fn (PaymentMethod $method) => ['value' => $method->value, 'label' => $method->label()])
                ->values(),
        ]);
    }

    /**
     * USR-03 — Simpan pesanan: kunci stok secara atomik lalu buat transaksi.
     */
    public function store(StoreCheckoutRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $service = Service::findOrFail($data['service_id']);
        $product = Product::findOrFail($data['product_id']);

        $quantity = (int) $data['quantity'];
        $isMandiri = $data['distribution_type'] === DistributionType::AlamatMandiri->value;

        $transaction = DB::transaction(function () use ($request, $data, $service, $product, $quantity, $isMandiri) {
            // Guard anti-oversell: hanya berkurang jika stok masih cukup.
            $affected = Product::whereKey($product->id)
                ->where('stock', '>=', $quantity)
                ->decrement('stock', $quantity);

            if ($affected === 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok tidak mencukupi.',
                ]);
            }

            return Transaction::create([
                'transaction_code' => Transaction::generateCode(Str::substr($service->name, 0, 3)),
                'user_id' => $request->user()->id,
                'service_id' => $service->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'total_amount' => $quantity * (float) $product->price,
                'distribution_type' => $data['distribution_type'],
                'recipient_name' => $isMandiri ? $data['recipient_name'] : null,
                'recipient_phone' => $isMandiri ? $data['recipient_phone'] : null,
                'recipient_province' => $isMandiri ? $data['recipient_province'] : null,
                'recipient_city' => $isMandiri ? $data['recipient_city'] : null,
                'recipient_district' => $isMandiri ? $data['recipient_district'] : null,
                'recipient_address' => $isMandiri ? $data['recipient_address'] : null,
                'payment_method' => $data['payment_method'],
                'payment_status' => PaymentStatus::Pending,
                'status' => TransactionStatus::Menunggu,
            ]);
        });

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('success', 'Pesanan berhasil dibuat. Silakan selesaikan pembayaran.');
    }
}
