<?php

namespace Steelze\ExchangeRate\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Steelze\ExchangeRate\ExchangeRate;

/**
 * Class ExchangeRateController
 *
 * @package Steelze\ExchangeRate\Http\Controllers
 */
class ExchangeRateController
{
    /**
     * Handle the exchange rate conversion request.
     *
     * @param  Request  $request
     * @param  ExchangeRate  $exchangeRate
     * @return JsonResponse
     */
    public function __invoke(Request $request, ExchangeRate $exchangeRate): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'currency' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['failed to convert']);
        }

        // Get the amount and currency from the request
        $amount = $request->amount;
        $currency = $request->currency;

        // Perform the currency conversion using the ExchangeRate service
        $data = $exchangeRate->convertToCurrency($amount, $currency);

        // Construct the response payload
        $payload = [
            'amount' => $amount,
            'from_currency' => 'EUR', // Assuming the default is Euro
            'to_currency' => $currency,
            'exchange_rate' => $data['exchange_rate'],
            'converted_amount' => $data['value'],
        ];

        // Return the response in JSON format
        return response()->json($payload);
    }
}
