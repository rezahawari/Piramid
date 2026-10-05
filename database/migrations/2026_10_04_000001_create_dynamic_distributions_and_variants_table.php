<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Opsi Lokasi Penyaluran / Distribusi Dinamis (CRUD Admin)
        Schema::create('distribution_options', function (Blueprint $table) {
            $table->id();
            $table->string('name_id');
            $table->string('name_en')->nullable();
            $table->string('name_zh')->nullable();
            $table->string('name_ar')->nullable();
            $table->text('description_id')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_zh')->nullable();
            $table->text('description_ar')->nullable();
            $table->decimal('fee_idr', 14, 2)->default(0);
            $table->decimal('fee_usd', 10, 2)->default(0);
            $table->decimal('fee_cny', 10, 2)->default(0);
            $table->decimal('fee_sar', 10, 2)->default(0);
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tambahan Opsi Pengolahan (Mentah / Matang) di tabel Layanan
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('has_cooking_option')->default(false)->after('has_sohibul');
            $table->enum('default_cooking_option', ['raw', 'cooked'])->default('raw')->after('has_cooking_option');
            $table->decimal('cooking_fee_idr', 14, 2)->default(0)->after('default_cooking_option');
            $table->decimal('cooking_fee_usd', 10, 2)->default(0)->after('cooking_fee_idr');
            $table->decimal('cooking_fee_cny', 10, 2)->default(0)->after('cooking_fee_usd');
            $table->decimal('cooking_fee_sar', 10, 2)->default(0)->after('cooking_fee_cny');
        });

        // 3. Multi-Mata Uang di tabel Produk
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price_usd', 10, 2)->nullable()->after('price');
            $table->decimal('price_cny', 10, 2)->nullable()->after('price_usd');
            $table->decimal('price_sar', 10, 2)->nullable()->after('price_cny');
        });

        // 4. Tabel Varian Produk Hewan Dinamis
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('name_id');
            $table->string('name_en')->nullable();
            $table->string('name_zh')->nullable();
            $table->string('name_ar')->nullable();
            $table->string('spec_description')->nullable(); // e.g. "35-40 kg, Jantan"
            $table->decimal('price_idr', 14, 2);
            $table->decimal('price_usd', 10, 2)->nullable();
            $table->decimal('price_cny', 10, 2)->nullable();
            $table->decimal('price_sar', 10, 2)->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Update tabel Transaksi
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable()->after('product_id')->constrained('product_variants')->nullOnDelete();
            $table->foreignId('distribution_option_id')->nullable()->after('distribution_type')->constrained('distribution_options')->nullOnDelete();
            $table->enum('cooking_option', ['raw', 'cooked'])->nullable()->after('distribution_option_id');
            $table->decimal('cooking_fee', 14, 2)->default(0)->after('cooking_option');
            $table->decimal('distribution_fee', 14, 2)->default(0)->after('cooking_fee');
            $table->string('currency', 10)->default('IDR')->after('distribution_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropForeign(['distribution_option_id']);
            $table->dropColumn([
                'product_variant_id',
                'distribution_option_id',
                'cooking_option',
                'cooking_fee',
                'distribution_fee',
                'currency',
            ]);
        });

        Schema::dropIfExists('product_variants');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['price_usd', 'price_cny', 'price_sar']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'has_cooking_option',
                'default_cooking_option',
                'cooking_fee_idr',
                'cooking_fee_usd',
                'cooking_fee_cny',
                'cooking_fee_sar',
            ]);
        });

        Schema::dropIfExists('distribution_options');
    }
};
