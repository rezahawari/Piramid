<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Dev-only admin account — ganti kredensial ini sebelum production,
        // atau pakai `php artisan user:make-admin {email}` di server.
        User::factory()->create([
            'name' => 'Admin Pyramid',
            'email' => 'admin@pyramid.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'User Demo',
            'email' => 'user@pyramid.test',
            'password' => 'password',
        ]);

        $qurban = Service::create([
            'name' => 'Qurban',
            'slug' => 'qurban',
            'description' => 'Tunaikan ibadah qurban dengan hewan terbaik, disalurkan transparan hingga penerima manfaat.',
            'is_active' => true,
        ]);

        $aqiqah = Service::create([
            'name' => 'Aqiqah',
            'slug' => 'aqiqah',
            'description' => 'Layanan aqiqah lengkap untuk menyambut kelahiran buah hati.',
            'is_active' => true,
        ]);

        $sedekah = Service::create([
            'name' => 'Sedekah',
            'slug' => 'sedekah',
            'description' => 'Sedekah hewan ternak untuk penerima manfaat terdaftar.',
            'is_active' => true,
        ]);

        $kambingStandar = Product::create([
            'name' => 'Kambing Standar',
            'slug' => 'kambing-standar',
            'description' => 'Kambing sehat, bobot 23-25 kg, memenuhi syarat qurban dan aqiqah.',
            'price' => 2500000,
            'weight_estimate_kg' => 24,
            'stock' => 50,
        ]);

        $kambingPremium = Product::create([
            'name' => 'Kambing Premium',
            'slug' => 'kambing-premium',
            'description' => 'Kambing pilihan, bobot 28-32 kg.',
            'price' => 3500000,
            'weight_estimate_kg' => 30,
            'stock' => 30,
        ]);

        $sapiPatungan = Product::create([
            'name' => 'Sapi 1/7 (Patungan)',
            'slug' => 'sapi-patungan',
            'description' => 'Satu bagian dari tujuh untuk qurban sapi bersama.',
            'price' => 3200000,
            'weight_estimate_kg' => 350,
            'stock' => 70,
        ]);

        $kambingStandar->services()->attach([$qurban->id, $aqiqah->id, $sedekah->id]);
        $kambingPremium->services()->attach([$qurban->id, $aqiqah->id]);
        $sapiPatungan->services()->attach([$qurban->id]);
    }
}
