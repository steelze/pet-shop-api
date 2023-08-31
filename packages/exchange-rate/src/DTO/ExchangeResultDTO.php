<?php

namespace Steelze\ExchangeRate\DTO;

readonly class ExchangeResultDTO
{
    protected string $fromCurrency;

    public function __construct(
        public readonly float $amount,
        public readonly string $toCurrency,
        public readonly float $exchangeRate,
        public readonly float $convertedAmount
    ) {
        $this->fromCurrency = 'EUR';
    }

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
