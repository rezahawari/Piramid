<?php

namespace App\Http\Requests;

use App\Enums\DistributionType;
use App\Enums\PaymentMethod;
use App\Models\Product;
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
            'quantity' => ['required', 'integer', 'min:1'],
            'distribution_type' => ['required', Rule::enum(DistributionType::class)],
            'distribution_location_note' => ['nullable', 'string', 'max:500'],
            'recipient_name' => ['nullable', 'required_if:distribution_type,'.DistributionType::AlamatMandiri->value, 'string', 'max:255'],
            'recipient_phone' => ['nullable', 'required_if:distribution_type,'.DistributionType::AlamatMandiri->value, 'string', 'max:30'],
            'recipient_province' => ['nullable', 'required_if:distribution_type,'.DistributionType::AlamatMandiri->value, 'string', 'max:100'],
            'recipient_city' => ['nullable', 'required_if:distribution_type,'.DistributionType::AlamatMandiri->value, 'string', 'max:100'],
            'recipient_district' => ['nullable', 'required_if:distribution_type,'.DistributionType::AlamatMandiri->value, 'string', 'max:100'],
            'recipient_address' => ['nullable', 'required_if:distribution_type,'.DistributionType::AlamatMandiri->value, 'string', 'max:1000'],
            'sohibul_names' => ['nullable', 'array'],
            'sohibul_names.*' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
        ];
    }

    /**
     * Validasi lintas-field: produk harus milik layanan, keduanya aktif,
     * kuantitas tidak melebihi stok saat ini, dan batas sohibul jika layanan memerlukan sohibul.
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

                if (! $service?->is_active) {
                    $validator->errors()->add('service_id', 'Layanan tidak tersedia.');

                    return;
                }

                if (! $product?->is_active) {
                    $validator->errors()->add('product_id', 'Produk tidak tersedia.');

                    return;
                }

                if (! $service->products()->whereKey($product->id)->exists()) {
                    $validator->errors()->add('product_id', 'Produk tidak tersedia pada layanan ini.');

                    return;
                }

                $qty = $this->integer('quantity');
                if ($qty > $product->stock) {
                    $validator->errors()->add('quantity', 'Stok tidak mencukupi.');
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
            'distribution_type' => 'tipe distribusi',
            'recipient_name' => 'nama penerima',
            'recipient_phone' => 'nomor telepon penerima',
            'recipient_province' => 'provinsi',
            'recipient_city' => 'kota/kabupaten',
            'recipient_district' => 'kecamatan',
            'recipient_address' => 'alamat lengkap',
            'payment_method' => 'metode pembayaran',
        ];
    }
}
