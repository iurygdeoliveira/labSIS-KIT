<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@labsis.dev.br',
            'email_verified_at' => now(),
            'password' => Hash::make('mudar123'),
        ]);
    }
}
