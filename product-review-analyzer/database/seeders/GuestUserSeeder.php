<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GuestUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'guest@product-review-analyzer.local'],
            [
                'name'     => 'Guest',
                'password' => Hash::make(Str::random(32)),
                'is_guest' => true,
            ]
        );
    }
}
