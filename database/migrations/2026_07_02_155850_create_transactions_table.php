<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('service_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_amount', 12, 2);

            $table->string('distribution_type'); // pt_yayasan | alamat_mandiri
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('recipient_province')->nullable();
            $table->string('recipient_city')->nullable();
            $table->string('recipient_district')->nullable();
            $table->text('recipient_address')->nullable();

            $table->string('payment_method'); // midtrans | manual_transfer
            $table->string('payment_status')->default('pending'); // pending|paid|rejected|expired|cancelled
            $table->string('status')->default('menunggu'); // menunggu|dibayar|hewan_disiapkan|tersembelih|didistribusikan

            $table->string('manual_transfer_proof_url')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();

            $table->string('midtrans_order_id')->nullable()->unique();
            $table->string('midtrans_snap_token')->nullable();
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('midtrans_payment_type')->nullable();
            $table->string('midtrans_va_number')->nullable();
            $table->string('midtrans_transaction_status')->nullable();
            $table->string('midtrans_fraud_status')->nullable();
            $table->timestamp('midtrans_settlement_time')->nullable();
            $table->json('midtrans_raw_response')->nullable();

            $table->timestamps();

            $table->index('payment_status');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
