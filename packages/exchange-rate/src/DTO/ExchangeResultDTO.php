<?php

namespace Steelze\ExchangeRate\DTO;

readonly class ExchangeResultDTO
{
    protected string $fromCurrency;

    public function __construct(
        public float $amount,
        public string $toCurrency,
        public float $exchangeRate,
        public float $convertedAmount
    ) {
        $this->fromCurrency = 'EUR';
    }

    /**
     * @return array<string, string|float>
     */
    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'from_currency' => $this->fromCurrency,
            'to_currency' => $this->toCurrency,
            'exchange_rate' => $this->exchangeRate,
            'converted_amount' => $this->convertedAmount,
        ];
    }
}
