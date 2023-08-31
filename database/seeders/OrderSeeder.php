<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::factory(100)->create()->each(function($order) {
            if (in_array($order->status->title, ['shipped', 'paid'])) {
                $payment = Payment::factory(1)->create()->first();
                $order->payment_uuid = $payment->uuid;
                $order->save();
            }
        });
    }
}
