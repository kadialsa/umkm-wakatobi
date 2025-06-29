<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as FakerFactory;
use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use App\Models\Brand;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Matikan cek FK agar bisa truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = FakerFactory::create('id_ID');

        $storeIds    = Store::pluck('id')->toArray();
        $categoryIds = Category::pluck('id')->toArray();
        $brandIds    = Brand::pluck('id')->toArray();

        foreach ($storeIds as $storeId) {
            for ($i = 0; $i < 5; $i++) {
                // Nama produk ala Indonesia
                $prefixes = ['Premium', 'Organik', 'Asli', 'Original', 'Homemade', 'Spesial', 'Halal', 'Ekspor', 'Tradisional', 'Terbaru'];
                $nouns    = ['Batik', 'Kopi', 'Teh', 'Keripik', 'Sate', 'Kacang', 'Madu', 'Kue', 'Roti', 'Jus', 'Sabun', 'Shampo', 'Tas', 'Sepatu', 'Kerajinan'];
                $name = $faker->boolean(70)
                    ? $faker->randomElement($prefixes) . ' ' . $faker->randomElement($nouns)
                    : $faker->randomElement($nouns);

                // Buat slug unik per store
                $baseSlug = Str::slug($name);
                $slug     = $baseSlug;
                $suffix   = 1;
                while (Product::where('store_id', $storeId)->where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $suffix;
                    $suffix++;
                }

                // Pilih category & brand—selalu ada
                $categoryId = $faker->randomElement($categoryIds);
                $brandId    = $faker->randomElement($brandIds);

                $regularPrice = $faker->randomFloat(2, 50, 2000);
                // Sale price selalu terisi, lebih kecil dari regular
                $salePrice = round($regularPrice * $faker->randomFloat(2, 0.5, 0.9), 2);

                // Gambar utama + tambahan
                $mainImage   = $faker->imageUrl(640, 480, 'technics');
                $extraImages = [];
                for ($j = 0; $j < 3; $j++) {
                    $extraImages[] = $faker->imageUrl(640, 480, 'technics');
                }

                Product::create([
                    'store_id'          => $storeId,
                    'name'              => $name,
                    'slug'              => $slug,
                    'short_description' => $faker->sentence(6),
                    'description'       => $faker->paragraphs(3, true),
                    'regular_price'     => $regularPrice,
                    'sale_price'        => $salePrice,
                    'SKU'               => strtoupper($faker->bothify('???-#####')),
                    'stock_status'      => $faker->randomElement(['instock', 'outofstock']),
                    'featured'          => $faker->boolean(20),
                    'quantity'          => $faker->numberBetween(1, 100),
                    'image'             => $mainImage,
                    'images'            => json_encode($extraImages),
                    'category_id'       => $categoryId,
                    'brand_id'          => $brandId,
                ]);
            }
        }

        $this->command->info('Seeded ' . Product::count() . ' products — semua field terisi penuh dengan slug unik.');
    }
}
