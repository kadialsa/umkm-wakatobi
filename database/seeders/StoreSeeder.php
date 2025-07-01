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

        $faker   = FakerFactory::create('id_ID');
        $userIds = User::where('utype', 'STR')->pluck('id')->toArray();

        if (count($userIds) < 10) {
            $this->command->error('Butuh minimal 10 user dengan utype=STR untuk membuat 10 toko.');
            return;
        }

        // Pastikan satu owner hanya sekali
        shuffle($userIds);
        $ownerIds = array_slice($userIds, 0, 10);

        foreach ($ownerIds as $ownerId) {
            $name = $faker->unique()->company;
            Store::create([
                'name'        => $name,
                'slug'        => Str::slug($name),
                'image'       => $faker->imageUrl(640, 480, 'business'),
                'description' => $faker->paragraph(3),
                'owner_id'    => $ownerId,
            ]);
        }

        $this->command->info('Seeded ' . Store::count() . ' stores with unique STR owners.');
    }
}
