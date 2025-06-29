<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


User::create([
    'name' => 'Admin',
    'email' => 'meormsukri@yahoo.com',
    'password' => Hash::make('admin'),
    'role' => 'admin',
    'approved' => true,
    'email_verified_at' => Carbon::now(),
]);

    }
}
