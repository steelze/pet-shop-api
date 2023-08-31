<?php

use Illuminate\Support\Facades\Route;
// use Steelze\ExchangeRate\Http\Controllers\ExchangeRateController;

Route::get('exchange-rate', function() {
  return response()->json(['Hello From Package']);
});
