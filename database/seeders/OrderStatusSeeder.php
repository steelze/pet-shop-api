<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['title' => 'canceled'],
            ['title' => 'shipped'],
            ['title' => 'paid'],
            ['title' => 'pending payment'],
            ['title' => 'open'],
        ];

        foreach ($statuses as $status) {
            OrderStatus::firstOrCreate($status);
        }
    }
}
