<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LandingHeroSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('landing_heroes')->truncate();

        DB::table('landing_heroes')->insert([
            [
                'title' => "Pilih Hewan Terbaik\nSesuai Syariat",
                'description' => "Hewan qurban & aqiqah terawat prima, sehat, bersertifikat dinas peternakan dan teruji syar'i.",
                'image_url' => "https://images.unsplash.com/photo-1546445317-29f4545e9d53?w=800&auto=format&fit=crop&q=80",
                'icon_name' => "verified_user_rounded",
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => "Laporan Foto & Video\nDokumentasi Realtime",
                'description' => "Pantau setiap proses mulai dari penyiapan, penyembelihan atas nama Anda, hingga pembagian daging.",
                'image_url' => "https://images.unsplash.com/photo-1524024973431-2ad916746881?w=800&auto=format&fit=crop&q=80",
                'icon_name' => "videocam_rounded",
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => "Penyaluran Tepat Sasaran\nke Pelosok & Dhuafa",
                'description' => "Menghadirkan senyum kebahagiaan bagi ribuan santri, yatim dhuafa, dan warga prasejahtera.",
                'image_url' => "https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800&auto=format&fit=crop&q=80",
                'icon_name' => "volunteer_activism_rounded",
                'order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
