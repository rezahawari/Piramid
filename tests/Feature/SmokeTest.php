<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Models\Product;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private User $admin;

    private Service $service;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->user = User::where('email', 'user@pyramid.test')->firstOrFail();
        $this->admin = User::where('email', 'admin@pyramid.test')->firstOrFail();
        $this->service = Service::where('slug', 'qurban')->firstOrFail();
        $this->product = Product::where('slug', 'kambing-standar')->firstOrFail();
    }

    public function test_public_pages_render(): void
    {
        $this->get('/')->assertOk();
        $this->get('/layanan/qurban')->assertOk();
        $this->get('/layanan/qurban/produk/kambing-standar')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_inactive_service_hidden_from_catalog(): void
    {
        $this->service->update(['is_active' => false]);
        $this->get('/layanan/qurban')->assertNotFound();
    }

    public function test_checkout_requires_auth(): void
    {
        $this->get('/layanan/qurban/produk/kambing-standar/checkout')
            ->assertRedirect('/login');
    }

    public function test_full_manual_transfer_checkout_flow(): void
    {
        $this->actingAs($this->user)
            ->get('/layanan/qurban/produk/kambing-standar/checkout')
            ->assertOk();

        $stockBefore = $this->product->stock;

        $response = $this->actingAs($this->user)->post('/checkout', [
            'service_id' => $this->service->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'distribution_type' => 'alamat_mandiri',
            'recipient_name' => 'Budi',
            'recipient_phone' => '0812345678',
            'recipient_province' => 'Jawa Barat',
            'recipient_city' => 'Bandung',
            'recipient_district' => 'Coblong',
            'recipient_address' => 'Jl. Dago 1',
            'payment_method' => 'manual_transfer',
        ]);

        $transaction = Transaction::latest('id')->first();
        $this->assertNotNull($transaction);
        $response->assertRedirect(route('transactions.show', $transaction));

        $this->assertSame($stockBefore - 2, $this->product->fresh()->stock);
        $this->assertSame(PaymentStatus::Pending, $transaction->payment_status);
        $this->assertSame(TransactionStatus::Menunggu, $transaction->status);
        $this->assertSame('Budi', $transaction->recipient_name);

        // Tracking page milik pembeli
        $this->actingAs($this->user)
            ->get("/transaksi/{$transaction->transaction_code}")
            ->assertOk();

        // User lain tidak boleh lihat
        $other = User::factory()->create();
        $this->actingAs($other)
            ->get("/transaksi/{$transaction->transaction_code}")
            ->assertForbidden();
    }

    public function test_checkout_address_required_for_alamat_mandiri(): void
    {
        $this->actingAs($this->user)->post('/checkout', [
            'service_id' => $this->service->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'distribution_type' => 'alamat_mandiri',
            'payment_method' => 'manual_transfer',
        ])->assertSessionHasErrors(['recipient_name', 'recipient_phone']);
    }

    public function test_checkout_rejects_oversell(): void
    {
        $this->actingAs($this->user)->post('/checkout', [
            'service_id' => $this->service->id,
            'product_id' => $this->product->id,
            'quantity' => $this->product->stock + 1,
            'distribution_type' => 'pt_yayasan',
            'payment_method' => 'manual_transfer',
        ])->assertSessionHasErrors(['quantity']);
    }

    public function test_admin_pages_render_and_are_gated(): void
    {
        // Non-admin diblokir
        $this->actingAs($this->user)->get('/admin')->assertForbidden();
        $this->actingAs($this->user)->get('/admin/layanan')->assertForbidden();

        // Admin bisa akses semua halaman back-office
        $this->actingAs($this->admin)->get('/admin')->assertOk();
        $this->actingAs($this->admin)->get('/admin/layanan')->assertOk();
        $this->actingAs($this->admin)->get('/admin/layanan/create')->assertOk();
        $this->actingAs($this->admin)->get("/admin/layanan/{$this->service->id}/edit")->assertOk();
        $this->actingAs($this->admin)->get('/admin/produk')->assertOk();
        $this->actingAs($this->admin)->get('/admin/produk/create')->assertOk();
        $this->actingAs($this->admin)->get("/admin/produk/{$this->product->id}/edit")->assertOk();
        $this->actingAs($this->admin)->get('/admin/transaksi')->assertOk();
    }

    public function test_admin_service_and_product_crud(): void
    {
        $this->actingAs($this->admin)->post('/admin/layanan', [
            'name' => 'Wakaf Hewan',
            'description' => 'Program baru',
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('services', ['name' => 'Wakaf Hewan']);

        $this->actingAs($this->admin)->post('/admin/produk', [
            'name' => 'Domba Super',
            'description' => 'Domba besar',
            'price' => 4000000,
            'weight_estimate_kg' => 35,
            'stock' => 10,
            'is_active' => true,
            'service_ids' => [$this->service->id],
        ]);
        $this->assertDatabaseHas('products', ['name' => 'Domba Super']);
        $domba = Product::where('name', 'Domba Super')->first();
        $this->assertTrue($domba->services->contains($this->service));
    }

    public function test_admin_approve_and_reject_manual_transfer(): void
    {
        $make = fn () => Transaction::create([
            'transaction_code' => Transaction::generateCode('QUR'),
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => $this->product->price,
            'total_amount' => $this->product->price,
            'distribution_type' => 'pt_yayasan',
            'payment_method' => 'manual_transfer',
            'manual_transfer_proof_url' => 'https://example.com/bukti.jpg',
        ]);

        // Approve
        $t1 = $make();
        $this->actingAs($this->admin)
            ->post("/admin/transaksi/{$t1->transaction_code}/setujui")
            ->assertRedirect();
        $t1->refresh();
        $this->assertSame(PaymentStatus::Paid, $t1->payment_status);
        // Stage "Dibayar" dilewati otomatis saat disetujui — langsung ke Hewan Disiapkan.
        $this->assertSame(TransactionStatus::HewanDisiapkan, $t1->status);
        $this->assertSame($this->admin->id, $t1->approved_by);

        // Reject + restore stock
        $t2 = $make();
        $stockBefore = $this->product->fresh()->stock;
        $this->actingAs($this->admin)
            ->post("/admin/transaksi/{$t2->transaction_code}/tolak", ['reason' => 'Bukti tidak valid'])
            ->assertRedirect();
        $t2->refresh();
        $this->assertSame(PaymentStatus::Rejected, $t2->payment_status);
        $this->assertSame('Bukti tidak valid', $t2->rejected_reason);
        $this->assertSame($stockBefore + 1, $this->product->fresh()->stock);
    }

    public function test_admin_status_advancement_rules(): void
    {
        $t = Transaction::create([
            'transaction_code' => Transaction::generateCode('QUR'),
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => $this->product->price,
            'total_amount' => $this->product->price,
            'distribution_type' => 'pt_yayasan',
            'payment_method' => 'manual_transfer',
        ]);

        // Detail transaksi admin render
        $this->actingAs($this->admin)
            ->get("/admin/transaksi/{$t->transaction_code}")
            ->assertOk();

        // Belum bayar → tidak boleh naik
        $this->actingAs($this->admin)
            ->post("/admin/transaksi/{$t->transaction_code}/status", ['status' => 'dibayar'])
            ->assertSessionHasErrors(['status']);

        // Simulasikan hasil approve(): lunas dan langsung lompat ke Hewan Disiapkan
        // (stage Dibayar dilewati otomatis, tidak butuh dokumentasi terpisah).
        $t->update(['payment_status' => PaymentStatus::Paid, 'status' => TransactionStatus::HewanDisiapkan]);

        // Lompat dua tahap → ditolak
        $this->actingAs($this->admin)
            ->post("/admin/transaksi/{$t->transaction_code}/status", ['status' => 'didistribusikan'])
            ->assertSessionHasErrors(['status']);

        // Naik tanpa dokumentasi tahap berjalan (Hewan Disiapkan) → ditolak
        $this->actingAs($this->admin)
            ->post("/admin/transaksi/{$t->transaction_code}/status", ['status' => 'tersembelih'])
            ->assertSessionHasErrors(['status']);

        // Simpan dokumentasi mode direct-upload (file_url), lalu naik → sukses
        $this->actingAs($this->admin)->post("/admin/transaksi/{$t->transaction_code}/dokumentasi", [
            'stage' => 'hewan_disiapkan',
            'type' => 'photo',
            'file_url' => 'https://example.com/foto.jpg',
            'caption' => 'Hewan diterima',
        ])->assertRedirect();
        $this->assertDatabaseHas('transaction_documentations', ['transaction_id' => $t->id, 'stage' => 'hewan_disiapkan']);

        $this->actingAs($this->admin)
            ->post("/admin/transaksi/{$t->transaction_code}/status", ['status' => 'tersembelih'])
            ->assertRedirect();
        $this->assertSame(TransactionStatus::Tersembelih, $t->fresh()->status);

        // Naik ke tahap terakhir tanpa dokumentasi tahap berjalan (Tersembelih) → ditolak
        $this->actingAs($this->admin)
            ->post("/admin/transaksi/{$t->transaction_code}/status", ['status' => 'didistribusikan'])
            ->assertSessionHasErrors(['status']);
    }

    public function test_midtrans_webhook_settlement_and_bad_signature(): void
    {
        config(['midtrans.server_key' => 'test-key']);

        $t = Transaction::create([
            'transaction_code' => Transaction::generateCode('QUR'),
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => $this->product->price,
            'total_amount' => $this->product->price,
            'distribution_type' => 'pt_yayasan',
            'payment_method' => 'midtrans',
        ]);

        $payload = [
            'order_id' => $t->transaction_code,
            'status_code' => '200',
            'gross_amount' => '2500000.00',
            'transaction_status' => 'settlement',
        ];
        $payload['signature_key'] = hash(
            'sha512',
            $payload['order_id'].$payload['status_code'].$payload['gross_amount'].'test-key',
        );

        $this->postJson('/webhooks/midtrans/notification', $payload)->assertOk();
        $t->refresh();
        $this->assertSame(PaymentStatus::Paid, $t->payment_status);
        // Stage "Dibayar" dilewati otomatis saat lunas via Midtrans — langsung ke Hewan Disiapkan.
        $this->assertSame(TransactionStatus::HewanDisiapkan, $t->status);

        $this->postJson('/webhooks/midtrans/notification', [
            'order_id' => $t->transaction_code,
            'signature_key' => 'bogus',
        ])->assertForbidden();
    }

    public function test_user_transaction_list_renders(): void
    {
        $this->actingAs($this->user)->get('/transaksi')->assertOk();
    }
}
