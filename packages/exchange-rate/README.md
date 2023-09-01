# Exchange Rate Converter

### Introduction
A collection REST API endpoints for FE team to build the UI of the Pet Shop App

## Installation

You can install the package via composer:

```bash
composer require steelze/currency-exchange-rate
```

## Usage

Make a `GET` request to `exchange-rate` with `amount` and `currency` as query parameters

```php
exchange-rate?amount=100&currency=USD

// Response
{
  "success": 1,
  "data": {
    "amount": 100,
    "from_currency": "EUR",
    "to_currency": "USD",
    "exchange_rate": 1.2345,
    "converted_amount": 123.45
  },
  "error": null,
  "errors": [],
  "extra": [],
  "trace": []
}
```


### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email sogungbure@gmail.com instead of using the issue tracker.

## Credits

-   [Odunayo Ogungbure](https://github.com/steelze)

### Author
Odunayo Ileri Ogungbure

### License 
MIT
