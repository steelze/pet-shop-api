<?php

namespace Steelze\ExchangeRate\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Steelze\ExchangeRate\ExchangeRate;
use Illuminate\Support\Facades\Validator;
use Steelze\ExchangeRate\Helpers\RespondWith;
use Symfony\Component\HttpFoundation\Response;
use Steelze\ExchangeRate\Exceptions\InvalidTargetCurrency;

/**
 * Class ExchangeRateController
 *
 * @package Steelze\ExchangeRate\Http\Controllers
 */
class ExchangeRateController
{
    /**
     * Handle the exchange rate conversion request.
     */
    public function __invoke(Request $request, ExchangeRate $exchangeRate): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'currency' => 'required|string',
        ]);

        if ($validator->fails()) {
            return RespondWith::error('Failed to convert', code: Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $result = $exchangeRate->convertToCurrency($request->amount, $request->currency);
        } catch (InvalidTargetCurrency $th) {
            return RespondWith::error(
                'Failed to convert: '.$th->getMessage(),
                code: Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\Throwable $th) {
            return RespondWith::error('Failed to convert', code: Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return RespondWith::success($result->toArray());
    }
}
