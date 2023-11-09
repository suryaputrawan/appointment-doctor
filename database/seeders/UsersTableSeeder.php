<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superUser = User::create([
            'name'              => 'Super Admin',
            'username'          => 'superadmin',
            'email'             => 'superadmin@mail.com',
            'email_verified_at' => now(),
            'password'          => bcrypt('password'),
            'remember_token'    => Str::random(30),
            'created_at'        => Carbon::now('Asia/Singapore'),
            'updated_at'        => Carbon::now('Asia/Singapore'),
        ]);

        $superUser->assignRole('Super Admin');
    }
}
