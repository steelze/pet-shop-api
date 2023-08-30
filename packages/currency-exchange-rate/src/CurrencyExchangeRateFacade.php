<?php

namespace Steelze\CurrencyExchangeRate;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Steelze\CurrencyExchangeRate\Skeleton\SkeletonClass
 */
class CurrencyExchangeRateFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'currency-exchange-rate';
    }
}
