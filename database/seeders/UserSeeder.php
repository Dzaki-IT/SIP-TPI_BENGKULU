<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tpi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pulauBaai = Tpi::where('kode_tpi', 'TPI-001')->first();
        $binaKarya = Tpi::where('kode_tpi', 'TPI-002')->first();

        User::updateOrCreate(
            ['email' => 'admin@tpi-bengkulu.test'],
            [
                'name' => 'Admin TPI Pulau Baai',
                'password' => Hash::make('password'),
                'role' => 'admin_tpi',
                'tpi_id' => $pulauBaai ? $pulauBaai->id : null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin2@tpi-bengkulu.test'],
            [
                'name' => 'Admin TPI Bina Karya',
                'password' => Hash::make('password'),
                'role' => 'admin_tpi',
                'tpi_id' => $binaKarya ? $binaKarya->id : null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'verifikator@tpi-bengkulu.test'],
            [
                'name' => 'Verifikator',
                'password' => Hash::make('password'),
                'role' => 'verifikator',
                'tpi_id' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'pimpinan@tpi-bengkulu.test'],
            [
                'name' => 'Pimpinan',
                'password' => Hash::make('password'),
                'role' => 'pimpinan',
                'tpi_id' => null,
            ]
        );
    }
}
