<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as FakerFactory;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Matikan cek FK agar truncate berhasil
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = FakerFactory::create();

        $parents = [
            'Makanan'   => ['Makanan Ringan', 'Makanan Berat'],
            'Minuman'   => ['Kopi', 'Teh', 'Jus'],
            'Kerajinan' => ['Anyaman', 'Kayu', 'Keramik'],
            'Pakaian'   => ['Batik', 'Kaos', 'Kemeja'],
            'Aksesori'  => ['Perhiasan', 'Tas', 'Topi'],
            'Elektronik' => ['Gadget', 'Aksesoris Elektronik'],
        ];

        foreach ($parents as $parentName => $children) {
            $parent = Category::create([
                'name'      => $parentName,
                'slug'      => Str::slug($parentName),
                // 'image'     => $faker->imageUrl(640, 480, 'food'),
                'image'     => '',
                'parent_id' => null,
            ]);

            foreach ($children as $childName) {
                Category::create([
                    'name'      => $childName,
                    'slug'      => Str::slug($parentName . ' ' . $childName),
                    // 'image'     => $faker->imageUrl(640, 480, 'product'),
                    'image'     => '',
                    'parent_id' => $parent->id,
                ]);
            }
        }
    }
}
