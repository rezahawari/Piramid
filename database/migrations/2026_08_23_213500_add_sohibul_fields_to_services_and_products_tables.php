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
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('has_sohibul')->default(false)->after('is_active');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->integer('max_sohibul')->default(1)->after('stock');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->json('sohibul_names')->nullable()->after('recipient_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('has_sohibul');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('max_sohibul');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('sohibul_names');
        });
    }
};
