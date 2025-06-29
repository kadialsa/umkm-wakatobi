<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;
use App\Models\Slide;

class SlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan cek FK dan kosongkan tabel
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Slide::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = FakerFactory::create('id_ID');

        // Buat 5 slide contoh
        for ($i = 1; $i <= 5; $i++) {
            Slide::create([
                'tagline'   => ucfirst($faker->words(2, true)),
                'title'     => ucfirst($faker->words(3, true)),
                'subtitle'  => $faker->sentence(6),
                'link'      => $faker->url,
                // File image ada di public/images/slides/slide-1.jpeg dst.
                'image'     => 'slide-' . $i . '.jpeg',
                'status'    => $faker->boolean(80), // 80% aktif
            ]);
        }

        $this->command->info('Seeded ' . Slide::count() . ' slides.');
    }
}
