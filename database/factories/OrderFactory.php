<?php

namespace Database\Factories;

use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = Product::select('uuid', 'price', DB::raw("FLOOR(RAND()*(5-1+1)+1) AS quantity"))
            ->inRandomOrder()
            ->limit(rand(1, 5))
            ->get();

        $total = $products->sum(function ($product) {
            return $product->price * (int) $product->quantity;
        });

        return [
            'user_uuid' => User::inRandomOrder()->value('uuid'),
            'order_status_uuid' => OrderStatus::inRandomOrder()->value('uuid'),
            'products' => $products->map(fn($product) => ['product' => $product->uuid, 'quantity' => (int) $product->quantity]),
            'address' => ['shipping' => fake()->address(), 'billing' => fake()->address()],
            'delivery_fee' => $total < 500 ? 15 : null,
            'amount' => $total,
            'shipped_at' => fake()->dateTime(),
        ];
    }
}
