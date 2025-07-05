<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key check
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate tabel
        DB::table('users')->truncate();

        // Aktifkan kembali foreign key check
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Data admin
        DB::table('users')->insert([
            'name'              => 'Administrator',
            'email'             => 'admin@example.com',
            'mobile'            => '081234567890',
            'email_verified_at' => now(),
            'password'          => Hash::make('admin123'),
            'utype'             => 'ADM',
            'remember_token'    => Str::random(10),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // Data customer
        DB::table('users')->insert([
            'name'              => 'John Doe',
            'email'             => 'john@example.com',
            'mobile'            => '081234567891',
            'email_verified_at' => now(),
            'password'          => Hash::make('user123'),
            'utype'             => 'USR',
            'remember_token'    => Str::random(10),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // Data Store (10 akun)
        for ($i = 1; $i <= 10; $i++) {
            DB::table('users')->insert([
                'name'              => 'Store ' . $i,
                'email'             => 'store' . $i . '@example.com',
                'mobile'            => '08123456789' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'email_verified_at' => now(),
                'password'          => Hash::make('store123'),
                'utype'             => 'STR',
                'remember_token'    => Str::random(10),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }

        // Data dummy (10 user biasa)
        for ($i = 1; $i <= 10; $i++) {
            DB::table('users')->insert([
                'name'              => 'User ' . $i,
                'email'             => 'user' . $i . '@example.com',
                'mobile'            => '0812' . rand(10000000, 99999999),
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'utype'             => 'USR',
                'remember_token'    => Str::random(10),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }
}
