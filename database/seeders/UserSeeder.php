<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Demo User',
            'email' => 'demo@stockflow.com',
            'password' => Hash::make('stockflow-demo'),
        ]);

        User::factory(10)->create();
    }
}
