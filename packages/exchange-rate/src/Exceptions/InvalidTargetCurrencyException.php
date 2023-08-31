<?php

namespace Steelze\ExchangeRate\Exceptions;

use Exception;

class InvalidTargetCurrencyException extends Exception
{
    /**
     * {@inheritdoc}
     */
    protected $message = 'The provided target currency is not valid.';
}
