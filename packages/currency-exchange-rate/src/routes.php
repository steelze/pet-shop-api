<?php

use Illuminate\Support\Facades\Route;
// use Steelze\CurrencyExchangeRate\Http\Controllers\CurrencyExchangeController;

Route::get('hello-world', function() {
  return response()->json(['Hello From Package']);
});
