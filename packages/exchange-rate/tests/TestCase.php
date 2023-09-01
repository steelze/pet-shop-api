<?php

namespace Steelze\ExchangeRate\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Steelze\ExchangeRate\ExchangeRateServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [ExchangeRateServiceProvider::class];
    }
}
