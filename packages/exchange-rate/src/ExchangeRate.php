<?php

namespace Steelze\ExchangeRate;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;
use Steelze\ExchangeRate\Exceptions\ExchangeRateFetchException;
use Steelze\ExchangeRate\Exceptions\InvalidTargetCurrencyException;

class ExchangeRate
{
    CONST EXCHANGE_RATES_URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xmls';


    /**
     * Fetches exchange rates from the European Central Bank.
     *
     * @return array
     * @throws ExchangeRateFetchException
     */
    protected function fetchExchangeRates(): array
    {
        try {
            $response = Http::get(self::EXCHANGE_RATES_URL)->throw();
            $xml = new SimpleXMLElement($response->body());

            $exchangeRates = [];

            foreach ($xml->Cube->Cube->Cube as $cube) {
                $currency = (string) $cube['currency'];
                $rate = (float) $cube['rate'];

                $exchangeRates[$currency] = $rate;
            }

            return $exchangeRates;
        } catch (RequestException $e) {
            throw new ExchangeRateFetchException('Error fetching exchange rates', $e->getCode(), $e);
        } catch (\Exception $e) {
            throw new ExchangeRateFetchException('An error occurred while fetching exchange rates', $e->getCode(), $e);
        }
    }

    public function convertToCurrency(float $amount, string $currency): array
    {
        $exchangeRates = $this->fetchExchangeRates();

        throw_if(!isset($exchangeRates[$currency]), new InvalidTargetCurrencyException);

        $rate = $exchangeRates[$currency];

        $value = $amount * $rate;

        return ['exchange_rate' => $rate, 'value' => $value];
    }
}
