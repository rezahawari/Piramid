<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->input('slug') ?: (string) $this->input('name')),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($this->route('produk')),
            ],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'price_usd' => ['nullable', 'numeric', 'min:0'],
            'price_cny' => ['nullable', 'numeric', 'min:0'],
            'price_sar' => ['nullable', 'numeric', 'min:0'],
            'weight_estimate_kg' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'max_sohibul' => ['required', 'integer', 'min:1'],
            'primary_image_url' => ['nullable', 'string', 'max:2048'],
            'image_file' => ['nullable', 'image', 'max:5120'], // Max 5MB
            'is_active' => ['boolean'],
            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['integer', Rule::exists('services', 'id')],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer'],
            'variants.*.name_id' => ['required', 'string', 'max:255'],
            'variants.*.name_en' => ['nullable', 'string', 'max:255'],
            'variants.*.name_zh' => ['nullable', 'string', 'max:255'],
            'variants.*.name_ar' => ['nullable', 'string', 'max:255'],
            'variants.*.spec_description' => ['nullable', 'string', 'max:255'],
            'variants.*.price_idr' => ['required', 'numeric', 'min:0'],
            'variants.*.price_usd' => ['nullable', 'numeric', 'min:0'],
            'variants.*.price_cny' => ['nullable', 'numeric', 'min:0'],
            'variants.*.price_sar' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['required', 'integer', 'min:0'],
            'variants.*.order' => ['nullable', 'integer'],
            'variants.*.is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama produk',
            'slug' => 'slug',
            'description' => 'deskripsi',
            'price' => 'harga',
            'weight_estimate_kg' => 'estimasi berat (kg)',
            'stock' => 'stok',
            'primary_image_url' => 'URL gambar utama',
            'image_file' => 'file gambar produk',
            'service_ids' => 'layanan',
        ];
    }
}
