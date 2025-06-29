<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;
use App\Models\Address;
use App\Models\User;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Matikan cek FK agar bisa truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Address::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = FakerFactory::create('id_ID'); // locale Indonesia

        // Ambil ID user admin@example.com
        $adminId = User::where('email', 'admin@example.com')->value('id');

        if (! $adminId) {
            $this->command->error('User with email admin@example.com not found.');
            return;
        }

        // Buat 1–3 alamat untuk user admin
        foreach (range(1, rand(1, 3)) as $i) {
            Address::create([
                'user_id'  => $adminId,
                'name'     => $faker->name(),
                'phone'    => $faker->phoneNumber(),
                'zip'      => $faker->postcode(),
                'state'    => $faker->state(),
                'city'     => $faker->city(),
                'address'  => $faker->streetAddress(),
                'locality' => $faker->streetName(),        // nama jalan/komplek
                'landmark' => $faker->sentence(3),         // landmark deskriptif
                'country'  => 'Indonesia',
            ]);
        }

        $this->command->info('Seeded ' . Address::count() . ' addresses for admin@example.com');
    }
}
