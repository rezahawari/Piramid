<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use App\Models\DistributionOption;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'distribution_option_id' => ['required', 'integer', 'exists:distribution_options,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'cooking_option' => ['nullable', 'string', 'in:raw,cooked'],
            'currency' => ['nullable', 'string', 'in:IDR,USD,CNY,SAR'],
            'distribution_location_note' => ['nullable', 'string', 'max:500'],
            'sohibul_names' => ['nullable', 'array'],
            'sohibul_names.*' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
        ];
    }

    /**
     * Validasi ketersediaan layanan, produk, varian, dan kecukupan stok.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $service = Service::find($this->integer('service_id'));
                $product = Product::find($this->integer('product_id'));
                $variantId = $this->input('product_variant_id');
                $variant = $variantId ? ProductVariant::find($variantId) : null;
                $distribution = DistributionOption::find($this->integer('distribution_option_id'));

                if (! $service?->is_active) {
                    $validator->errors()->add('service_id', 'Layanan tidak tersedia.');
                    return;
                }

                if (! $product?->is_active) {
                    $validator->errors()->add('product_id', 'Produk tidak tersedia.');
                    return;
                }

                if (! $distribution?->is_active) {
                    $validator->errors()->add('distribution_option_id', 'Opsi penyaluran tidak tersedia.');
                    return;
                }

                if (! $service->products()->whereKey($product->id)->exists()) {
                    $validator->errors()->add('product_id', 'Produk tidak tersedia pada layanan ini.');
                    return;
                }

                $qty = $this->integer('quantity');

                if ($variant) {
                    if ($variant->product_id !== $product->id || ! $variant->is_active) {
                        $validator->errors()->add('product_variant_id', 'Varian produk tidak valid atau tidak aktif.');
                        return;
                    }
                    if ($qty > $variant->stock) {
                        $validator->errors()->add('quantity', "Stok varian {$variant->name_id} tidak mencukupi (sisa {$variant->stock}).");
                        return;
                    }
                } else {
                    if ($product->variants()->active()->exists()) {
                        $validator->errors()->add('product_variant_id', 'Harap pilih salah satu varian yang tersedia.');
                        return;
                    }
                    if ($qty > $product->stock) {
                        $validator->errors()->add('quantity', 'Stok tidak mencukupi.');
                        return;
                    }
                }

                // Validasi batasan sohibul
                if ($service->has_sohibul) {
                    $maxAllowed = $qty * ($product->max_sohibul ?? 1);
                    $names = array_values(array_filter((array) $this->input('sohibul_names', [])));

                    if (empty($names)) {
                        $validator->errors()->add('sohibul_names', 'Harap masukkan minimal 1 nama sohibul (atas nama qurban/aqiqah).');
                    } elseif (count($names) > $maxAllowed) {
                        $validator->errors()->add(
                            'sohibul_names',
                            "Maksimal nama sohibul untuk {$qty} ekor {$product->name} adalah {$maxAllowed} orang ({$product->max_sohibul} orang/ekor)."
                        );
                    }
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'quantity' => 'jumlah',
            'product_variant_id' => 'varian produk',
            'distribution_option_id' => 'opsi penyaluran',
            'cooking_option' => 'opsi pengolahan daging',
            'payment_method' => 'metode pembayaran',
        ];
    }
}
