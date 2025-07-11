<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Veterinarian;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        // $admin = User::create([
        //     'name' => 'System Admin',
        //     'email' => 'admin@vrms.com',
        //     'password' => Hash::make('@dminVRMS!'),
        //     'role' => 'admin',
        // ]);

        // Create veterinarian profile for admin
        // Veterinarian::create([
        //     'user_id' => $admin->id,
        //     'email' => 'vet@vrms.com',
        //     'password' => Hash::make('@dminVRMS!'),
        //     'name' => 'Juan Dela Cruz',
        //     'license_number' => 'ADMIN-001',
        //     'specialization' => 'System Administration',
        //     'phone' => '09123456789',
        //     'is_admin' => true,
        // ]);

        $this->command->info('Admin user created:');
        $this->command->info('Email: admin@vrms.com');
        $this->command->info('Password: @dminVRMS!');
    }
}
