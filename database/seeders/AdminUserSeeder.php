<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed or update admin users safely without touching any course or student data.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Amar Bangla Academy Admin',
                'email' => 'admin@amar-bangla.edu',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Amar Bangla Academy Admin',
                'email' => 'admin@amarbanglaacademy.com',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($admins as $adminData) {
            $user = User::updateOrCreate(
                ['email' => $adminData['email']],
                [
                    'name' => $adminData['name'],
                    'password' => $adminData['password'],
                ]
            );

            if (isset($this->command)) {
                $this->command->info("Admin user ensured: {$user->email} (Password: password123)");
            }
        }
    }
}
