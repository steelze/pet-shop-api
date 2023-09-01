<?php

namespace Steelze\ExchangeRate\Tests\Unit;

use Steelze\ExchangeRate\DTO\ExchangeResultDTO;
use Steelze\ExchangeRate\ExchangeRate;
use Steelze\ExchangeRate\Exceptions\CouldNotFetchExchangeRates;
use Steelze\ExchangeRate\Exceptions\InvalidTargetCurrency;
use Illuminate\Support\Facades\Http;
use Mockery;
use Steelze\ExchangeRate\Tests\TestCase;

class ExchangeRateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
    }

    private function getFakeExchangeRateXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
            <Envelope>
                <Cube>
                    <Cube time="2023-09-01">
                        <Cube currency="USD" rate="1.2345"/>
                        <Cube currency="GBP" rate="1.0000"/>
                    </Cube>
                </Cube>
            </Envelope>';
    }

    public function test_can_fetch_exchange_rates()
    {
        Http::fake([
            ExchangeRate::EXCHANGE_RATES_URL => Http::response($this->getFakeExchangeRateXml(), 200),
        ]);

        $exchangeRate = new ExchangeRate();
        $rates = $exchangeRate->fetchExchangeRates();

        $this->assertIsArray($rates);
        $this->assertArrayHasKey('USD', $rates);
        $this->assertArrayHasKey('GBP', $rates);
    }

    public function test_can_convert_to_currency()
    {
        Http::fake([
            ExchangeRate::EXCHANGE_RATES_URL => Http::response($this->getFakeExchangeRateXml(), 200),
        ]);

        $exchangeRate = Mockery::mock(ExchangeRate::class)->makePartial();

        $exchangeRate->shouldReceive('fetchExchangeRates')->andReturn(['USD' => 1.2345]);

        $amount = 100.0;
        $currency = 'USD';

        $result = $exchangeRate->convertToCurrency($amount, $currency);

        $this->assertInstanceOf(ExchangeResultDTO::class, $result);
        $this->assertEquals($amount, $result->amount);
        $this->assertEquals($currency, $result->toCurrency);
        $this->assertEquals(1.2345, $result->exchangeRate);
        $this->assertEquals($amount * 1.2345, $result->convertedAmount);

        Mockery::close();
    }

    public function test_throws_an_exception_for_invalid_currency()
    {
        Http::fake([
            ExchangeRate::EXCHANGE_RATES_URL => Http::response($this->getFakeExchangeRateXml(), 200),
        ]);

        $exchangeRate = Mockery::mock(ExchangeRate::class)->makePartial();

        $exchangeRate->shouldReceive('fetchExchangeRates')->andReturn(['USD' => 1.2345]);

        $this->expectException(InvalidTargetCurrency::class);

        $amount = 100.0;
        $currency = 'GBP';

        $exchangeRate->convertToCurrency($amount, $currency);

        Mockery::close();
    }

    public function test_handles_fetch_exchange_rates_exception()
    {
        $exchangeRate = new ExchangeRate();

        // Mock HTTP request to throw an exception
        Http::fake([
            ExchangeRate::EXCHANGE_RATES_URL => Http::throw(fn() =>
                new CouldNotFetchExchangeRates('Error fetching exchange rates', 500)
            ),
        ]);

        $this->expectException(CouldNotFetchExchangeRates::class);

        $exchangeRate->fetchExchangeRates();
    }
}
