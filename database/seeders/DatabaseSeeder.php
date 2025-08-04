<?php

namespace Database\Seeders;


use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\MainCategories;
use App\Models\SubCategories;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory(10)->create();
            Brand::factory(5)->has(
                MainCategories::factory(2)->has(
                    SubCategories::factory(3)->has(
                        Product::factory(4)
        )
    )
)->create();
    }
}
