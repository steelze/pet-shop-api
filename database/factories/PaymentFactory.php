<?php

namespace Database\Factories;

use App\Enums\PaymentTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(PaymentTypeEnum::cases());

        $details = match ($type) {
            PaymentTypeEnum::CREDIT_CARD => [
                'holder_name' => fake()->name(),
                'number' => fake()->creditCardNumber(),
                'ccv' => fake()->randomNumber(3, true),
                'expire_date' => fake()->creditCardExpirationDateString(),
            ],
            PaymentTypeEnum::CASH_ON_DELIVERY => [
                'first_name' => fake()->firstName(),
                'last_name' => fake()->firstName(),
                'address' => fake()->address(),
            ],
            PaymentTypeEnum::BANK_TRANSFER => [
                'name' => fake()->name(),
                'swift' => fake()->swiftBicNumber(),
                'iban' => fake()->iban(),
            ],
            default => [],
        };

        return [
            'type' => $type,
            'details' => $details,
        ];
    }
}
