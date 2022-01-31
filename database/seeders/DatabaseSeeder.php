<?php

namespace Database\Seeders;

use App\Models\Images;
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
        // \App\Models\User::factory(10)->create();
        Images::create(['route' => 'anonymousUser.jpg']);
        $this->call(AdminSeeder::class);
        $this->call(CategorySeeder::class);
    }
}
