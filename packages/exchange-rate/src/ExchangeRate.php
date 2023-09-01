<?php

namespace Steelze\ExchangeRate;

use SimpleXMLElement;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Steelze\ExchangeRate\DTO\ExchangeResultDTO;
use Steelze\ExchangeRate\Exceptions\InvalidTargetCurrency;
use Steelze\ExchangeRate\Exceptions\CouldNotFetchExchangeRates;

class ExchangeRate
{
    const EXCHANGE_RATES_URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    /**
     * Fetches exchange rates from the European Central Bank.
     *
     * @return array<float, string>
     * @throws CouldNotFetchExchangeRates
     */
    public function fetchExchangeRates(): array
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
            throw new CouldNotFetchExchangeRates('Error fetching exchange rates', $e->getCode(), $e);
        } catch (\Exception $e) {
            throw new CouldNotFetchExchangeRates('An error occurred while fetching exchange rates', $e->getCode(), $e);
        }
    }

    /**
     * Converts the given amount to the specified currency.
     *
     * @throws InvalidTargetCurrency
     * @throws CouldNotFetchExchangeRates
     */
    public function convertToCurrency(float $amount, string $currency): ExchangeResultDTO
    {
        $exchangeRates = $this->fetchExchangeRates();

        if (!isset($exchangeRates[$currency])) {
            throw new InvalidTargetCurrency();
        }

        $rate = $exchangeRates[$currency];
        $convertedAmount = $amount * $rate;

        return new ExchangeResultDTO(
            amount: $amount,
            toCurrency: $currency,
            exchangeRate: $rate,
            convertedAmount: $convertedAmount,
        );
    }
}
