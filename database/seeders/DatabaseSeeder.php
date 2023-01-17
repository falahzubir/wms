<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // admin user
        \App\Models\User::create([
            'name' => 'SuperAdmin',
            'staff_id' => config('app.staff_prefix') . '0001',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);

        \App\Models\Bucket::factory(10)->create();
        $this->call([
            ProductSeeder::class,
            CompanySeeder::class,
        ]);

    }
}
