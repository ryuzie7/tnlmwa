<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@uitm.edu.my',
            'phone' => '0123456789',
            'staff_id' => 'STAFF001',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'approved' => true
        ]);
    }
}
