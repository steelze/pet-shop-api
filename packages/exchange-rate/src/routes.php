<?php

use Illuminate\Support\Facades\Route;
use Steelze\ExchangeRate\Http\Controllers\ExchangeRateController;

Route::get('exchange-rate', ExchangeRateController::class);
