<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
<<<<<<< HEAD
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

class UserSeeder extends Seeder
{
        protected static $password;
=======

class UserSeeder extends Seeder
{
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
<<<<<<< HEAD
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'phone' => '0112332424',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('1234567'),
            'role' => 'super_admin',
            'remember_token' => Str::random(10),
        ]);
=======
        //
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
    }
}
