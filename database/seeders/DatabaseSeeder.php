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
        // Tanpa factory/Faker — seeder harus bisa jalan di image production
        // yang di-build dengan composer --no-dev.

        // Dev-only admin account — ganti kredensial ini sebelum production,
        // atau pakai `php artisan user:make-admin {email}` di server.
        User::firstOrCreate(
            ['email' => 'admin@pyramid.test'],
            [
                'name' => 'Admin Pyramid',
                'password' => 'password',
                'role' => 'admin',
            ],
        );

        User::firstOrCreate(
            ['email' => 'user@pyramid.test'],
            [
                'name' => 'User Demo',
                'password' => 'password',
            ],
        );

        $qurban = Service::firstOrCreate(
            ['slug' => 'qurban'],
            [
                'name' => 'Qurban',
                'description' => 'Tunaikan ibadah qurban dengan hewan terbaik, disalurkan transparan hingga penerima manfaat.',
                'is_active' => true,
            ],
        );

        $aqiqah = Service::firstOrCreate(
            ['slug' => 'aqiqah'],
            [
                'name' => 'Aqiqah',
                'description' => 'Layanan aqiqah lengkap untuk menyambut kelahiran buah hati.',
                'is_active' => true,
            ],
        );

        $sedekah = Service::firstOrCreate(
            ['slug' => 'sedekah'],
            [
                'name' => 'Sedekah',
                'description' => 'Sedekah hewan ternak untuk penerima manfaat terdaftar.',
                'is_active' => true,
            ],
        );

        $kambingStandar = Product::firstOrCreate(
            ['slug' => 'kambing-standar'],
            [
                'name' => 'Kambing Standar',
                'description' => 'Kambing sehat, bobot 23-25 kg, memenuhi syarat qurban dan aqiqah.',
                'price' => 2500000,
                'weight_estimate_kg' => 24,
                'stock' => 50,
            ],
        );

        $kambingPremium = Product::firstOrCreate(
            ['slug' => 'kambing-premium'],
            [
                'name' => 'Kambing Premium',
                'description' => 'Kambing pilihan, bobot 28-32 kg.',
                'price' => 3500000,
                'weight_estimate_kg' => 30,
                'stock' => 30,
            ],
        );

        $sapiPatungan = Product::firstOrCreate(
            ['slug' => 'sapi-patungan'],
            [
                'name' => 'Sapi 1/7 (Patungan)',
                'description' => 'Satu bagian dari tujuh untuk qurban sapi bersama.',
                'price' => 3200000,
                'weight_estimate_kg' => 350,
                'stock' => 70,
            ],
        );

        $kambingStandar->services()->syncWithoutDetaching([$qurban->id, $aqiqah->id, $sedekah->id]);
        $kambingPremium->services()->syncWithoutDetaching([$qurban->id, $aqiqah->id]);
        $sapiPatungan->services()->syncWithoutDetaching([$qurban->id]);
    }
}
