<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as FakerFactory;
use App\Models\Store;
use App\Models\User;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Matikan cek FK agar bisa truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Store::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = FakerFactory::create('id_ID');

        // Ambil hanya user dengan utype = 'STR'
        $userIds = User::where('utype', 'STR')->pluck('id')->toArray();
        if (empty($userIds)) {
            $this->command->error('Tidak ada user dengan utype=STR. Pastikan UserSeeder sudah membuat akun store.');
            return;
        }

        // Buat 10 toko, setiap toko wajib punya owner
        for ($i = 0; $i < 10; $i++) {
            $name = $faker->unique()->company;
            Store::create([
                'name'        => $name,
                'slug'        => Str::slug($name),
                'image'       => $faker->imageUrl(640, 480, 'business'),
                'description' => $faker->paragraph(3),
                'owner_id'    => $faker->randomElement($userIds), // selalu ada owner
            ]);
        }

        $this->command->info('Seeded ' . Store::count() . ' stores with STR owners.');
    }
}
