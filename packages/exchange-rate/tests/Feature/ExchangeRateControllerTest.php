<?php

namespace Steelze\ExchangeRate\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Testing\Fluent\AssertableJson;
use Steelze\ExchangeRate\DTO\ExchangeResultDTO;
use Steelze\ExchangeRate\Exceptions\InvalidTargetCurrency;
use Steelze\ExchangeRate\ExchangeRate;
use Steelze\ExchangeRate\Helpers\RespondWith;
use Steelze\ExchangeRate\Http\Controllers\ExchangeRateController;
use Steelze\ExchangeRate\Tests\TestCase;

class ExchangeRateControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_can_handle_successful_exchange_rate_conversion_request()
    {
        $this->withoutExceptionHandling();

        $exchangeRate = $this->mock(ExchangeRate::class);

        $exchangeRate->shouldReceive('convertToCurrency')
            ->with(100.0, 'USD')
            ->once()
            ->andReturn(new ExchangeResultDTO(
                amount: 100.0,
                toCurrency: 'USD',
                exchangeRate: 1.2345,
                convertedAmount: 123.45
            ));

        $response = $this->get('exchange-rate?'.http_build_query(['amount' => 100.0, 'currency' => 'USD']));

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['success', 'data'])->etc();
            })
            ->assertJsonFragment([
                'amount' => 100.0,
                'from_currency' => 'EUR',
                'to_currency' => 'USD',
                'exchange_rate' => 1.2345,
                'converted_amount' => 123.45,
            ]);
    }

    public function test_handles_failed_validation()
    {
        $response = $this->get('exchange-rate?'.http_build_query(['currency' => 'USD']));

        $response->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['success', 'error'])->etc();
            });
    }

    public function test_handles_failed_currency_conversion()
    {
        $exchangeRate = $this->mock(ExchangeRate::class);

        $exchangeRate->shouldReceive('convertToCurrency')
            ->with(100.0, 'GBP')
            ->once()
            ->andThrow(new InvalidTargetCurrency());

        $response = $this->get('exchange-rate?'.http_build_query(['amount' => 100.0, 'currency' => 'GBP']));

        $response->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['success', 'error'])->etc();
            });
    }

    public function test_handles_other_exceptions()
    {
        $exchangeRate = $this->mock(ExchangeRate::class);

        $exchangeRate->shouldReceive('convertToCurrency')
            ->with(100.0, 'USD')
            ->once()
            ->andThrow(new \Exception('Some generic exception'));

        $response = $this->get('exchange-rate?'.http_build_query(['amount' => 100.0, 'currency' => 'USD']));

        $response->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['success', 'error'])->etc();
            });
    }
}
