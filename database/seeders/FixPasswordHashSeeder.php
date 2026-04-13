<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class FixPasswordHashSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with properly hashed passwords.
     */
    public function run(): void
    {
        // Fix all existing users - reset their passwords to a default bcrypt hashed password
        DB::table('users')->update([
            'password' => Hash::make('password123'), // Change this to your desired default password
        ]);

        echo "All user passwords have been reset and hashed with Bcrypt.\n";
        echo "Default password: password123\n";
        echo "Please change your password after login!\n";
    }
}
