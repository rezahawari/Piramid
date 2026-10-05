<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
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
            'has_sohibul' => $this->boolean('has_sohibul'),
            'has_cooking_option' => $this->boolean('has_cooking_option'),
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
                Rule::unique('services', 'slug')->ignore($this->route('layanan')),
            ],
            'description' => ['nullable', 'string'],
            'cover_image_url' => ['nullable', 'string', 'max:2048'],
            'image_file' => ['nullable', 'image', 'max:5120'],
            'is_active' => ['boolean'],
            'has_sohibul' => ['boolean'],
            'has_cooking_option' => ['boolean'],
            'default_cooking_option' => ['nullable', 'string', 'in:raw,cooked'],
            'cooking_fee_idr' => ['nullable', 'numeric', 'min:0'],
            'cooking_fee_usd' => ['nullable', 'numeric', 'min:0'],
            'cooking_fee_cny' => ['nullable', 'numeric', 'min:0'],
            'cooking_fee_sar' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama layanan',
            'slug' => 'slug',
            'description' => 'deskripsi',
            'cover_image_url' => 'URL gambar sampul',
            'image_file' => 'file gambar sampul',
        ];
    }
}
