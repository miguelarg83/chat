<?php

namespace Database\Seeders;

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
        // \App\Models\User::factory(2)->hasCategories(3)->create();
        \App\Models\User::factory(2)->has(
            \App\Models\Category::factory(3) // random_int(2,3)
                ->has(\App\Models\Product::factory(10)
                    ->has(\App\Models\Image::factory(2)))
        )->create();
    }
}
