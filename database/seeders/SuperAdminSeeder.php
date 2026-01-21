<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@leoni.com',
            'password' => Hash::make('password'), // Replace with a secure password
            'role' => 'super_admin',
            'service_id' => null, // Set if needed
            'email_verified_at' => now(),
        ]);
    }
}
