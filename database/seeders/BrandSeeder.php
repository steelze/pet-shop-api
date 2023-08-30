<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                "title" => "wag",
                "slug" => "wag",
            ],
            [
                "title" => "royal canin",
                "slug" => "royal-canin",
            ],
            [
                "title" => "petsafe",
                "slug" => "petsafe",
            ],
            [
                "title" => "manapro",
                "slug" => "manapro",
            ],
            [
                "title" => "kitzy",
                "slug" => "kitzy",
            ],
            [
                "title" => "iris",
                "slug" => "iris",
            ],
            [
                "title" => "hills",
                "slug" => "hills",
            ],
            [
                "title" => "frontline",
                "slug" => "frontline",
            ],
            [
                "title" => "blue",
                "slug" => "blue",
            ],
            [
                "title" => "acana",
                "slug" => "acana",
            ],
        ];

        foreach ($brands as $brand) {
            Brand::firstOrCreate($brand);
        }
    }
}
