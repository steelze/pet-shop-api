<?php

namespace Steelze\ExchangeRate\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Steelze\ExchangeRate\Exceptions\InvalidTargetCurrencyException;
use Steelze\ExchangeRate\ExchangeRate;
use Steelze\ExchangeRate\Helpers\RespondWith;

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

        if ($validator->fails()) return RespondWith::error('Failed to convert');

        try {
            $result = $exchangeRate->convertToCurrency($request->amount, $request->currency);
        } catch (InvalidTargetCurrencyException $th) {
            return RespondWith::error('Failed to convert: '.$th->getMessage());
        } catch (\Throwable $th) {
            return RespondWith::error('Failed to convert');
        }

        return RespondWith::success($result->toArray());
    }
}
