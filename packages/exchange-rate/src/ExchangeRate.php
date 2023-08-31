<?php

namespace Steelze\ExchangeRate;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;
use Steelze\ExchangeRate\DTO\ExchangeResultDTO;
use Steelze\ExchangeRate\Exceptions\ExchangeRateFetchException;
use Steelze\ExchangeRate\Exceptions\InvalidTargetCurrencyException;

class ExchangeRate
{
    CONST EXCHANGE_RATES_URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    /**
     * Fetches exchange rates from the European Central Bank.
     *
     * @return array
     * @throws ExchangeRateFetchException
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
            throw new ExchangeRateFetchException('Error fetching exchange rates', $e->getCode(), $e);
        } catch (\Exception $e) {
            throw new ExchangeRateFetchException('An error occurred while fetching exchange rates', $e->getCode(), $e);
        }
    }

    /**
     * Converts the given amount to the specified currency.
     *
     * @param float $amount
     * @param string $currency
     * @return ExchangeResultDTO
     * @throws InvalidTargetCurrencyException
     * @throws ExchangeRateFetchException
     */
    public function convertToCurrency(float $amount, string $currency): ExchangeResultDTO
    {
        $exchangeRates = $this->fetchExchangeRates();

        if (!isset($exchangeRates[$currency])) {
            throw new InvalidTargetCurrencyException();
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
