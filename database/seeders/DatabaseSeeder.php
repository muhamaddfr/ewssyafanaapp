<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat User Pemilik & Keuangan jika belum ada (hanya akun pengguna)
        User::firstOrCreate(
            ['email' => 'pemilik@syafana.test'],
            [
                'kode'     => 'PML001',
                'nama'     => 'Pemilik Syafana',
                'password' => Hash::make('password'),
                'peran'    => 'Pemilik Usaha',
            ]
        );

        User::firstOrCreate(
            ['email' => 'keuangan@syafana.test'],
            [
                'kode'     => 'KEU001',
                'nama'     => 'Staf Keuangan',
                'password' => Hash::make('password'),
                'peran'    => 'Bagian Keuangan',
            ]
        );
    }
}
