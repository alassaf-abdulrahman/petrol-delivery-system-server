<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the admin user in the 'users' table
        $userID = DB::table('users')->insertGetId([
            'email' => 'admin@example.com', // Set a default admin email
            'password' => Hash::make('test'), // Set a default password, use Hash for security
            'name' => 'Admin User',
            'role' => 'admin', // Admin role
            'phoneNumber' => '1234567890', // Example phone number
        ]);

        // Create a corresponding record in the 'admins' table
        DB::table('admins')->insert([
            'userID' => $userID,
        ]);
    }
}
