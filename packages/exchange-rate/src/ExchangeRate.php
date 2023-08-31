<?php

namespace Steelze\ExchangeRate;

use Illuminate\Support\Facades\Http;
use SimpleXMLElement;
use Steelze\ExchangeRate\Exceptions\InvalidTargetCurrency;

class ExchangeRate
{
    protected array $rates = [];

    protected function fetchCurrencies()
    {
        $response = Http::get('https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml');
        if ($response->failed()) {
            throw new \Exception("Error fetching exchange rates");
        }

        $xml = new SimpleXMLElement($response);

        foreach ($xml->Cube->Cube->Cube as $cube) {
            $currency = (string) $cube['currency'];
            $rate = (float) $cube['rate'];

            $this->rates[$currency] = $rate;
        }
    }

    public function convertToCurrency(float $amount, string $currency): array
    {
        $this->fetchCurrencies();

        throw_if(!isset($this->rates[$currency]), new InvalidTargetCurrency);

        $rate = $this->rates[$currency];

        $value = $amount * $rate;

        return ['exchange_rate' => $rate, 'value' => $value];
    }
}
