<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Administrator::create([
            'name' => 'Administrator',
            'email' => 'admin@zcmc.com',
            'username' => 'administrator',
            'password' => bcrypt('4dm1n'),
        ]);
    }
}
