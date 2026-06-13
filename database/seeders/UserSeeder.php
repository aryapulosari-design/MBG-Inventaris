<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Super Administrator',
                'email'    => 'superadmin@mbg.id',
                'password' => Hash::make('password'),
                'role'     => 'super_admin',
            ],
            [
                'name'     => 'Admin Program MBG',
                'email'    => 'adminprogram@mbg.id',
                'password' => Hash::make('password'),
                'role'     => 'admin_program',
            ],
            [
                'name'     => 'Admin Dapur Utama',
                'email'    => 'admindapur@mbg.id',
                'password' => Hash::make('password'),
                'role'     => 'admin_dapur',
            ],
            [
                'name'     => 'Viewer Pemantau',
                'email'    => 'viewer@mbg.id',
                'password' => Hash::make('password'),
                'role'     => 'viewer',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['email' => $user['email']], $user);
        }

        $this->command->info('✅ Users seeded: superadmin, adminprogram, admindapur, viewer (password: "password")');
    }
}
