<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                "title" => "pet clean-up and odor control",
                "slug" => "pet-clean-up-and-odor-control",
            ],
            [
                "title" => "cat litter",
                "slug" => "cat-litter",
            ],
            [
                "title" => "wet pet food",
                "slug" => "wet-pet-food",
            ],
            [
                "title" => "pet oral care",
                "slug" => "pet-oral-care",
            ],
            [
                "title" => "heartworm medication",
                "slug" => "heartworm-medication",
            ],
            [
                "title" => "pet vitamins and supplements",
                "slug" => "pet-vitamins-and-supplements",
            ],
            [
                "title" => "pet grooming supplies",
                "slug" => "pet-grooming-supplies",
            ],
            [
                "title" => "flea and tick medication",
                "slug" => "flea-and-tick-medication",
            ],
            [
                "title" => "pet treats and chews",
                "slug" => "pet-treats-and-chews",
            ],
            [
                "title" => "dry dog food",
                "slug" => "dry-dog-food",
            ]
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate($category);
        }
    }
}
