<?php

namespace Steelze\ExchangeRate;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Steelze\ExchangeRate\Skeleton\SkeletonClass
 */
class ExchangeRateFacade extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'exchange-rate';
    }
}
