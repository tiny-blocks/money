# Money

[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

* [Overview](#overview)
* [Installation](#installation)
* [How to use](#how-to-use)
* [License](#license)
* [Contributing](#contributing)

<div id='overview'></div> 

## Overview

Value Object that represents a monetary value.

<div id='installation'></div>

## Installation

```bash
composer require tiny-blocks/money
```

<div id='how-to-use'></div>

## How to use

The library exposes a concrete implementation for representing and performing monetary operations.

### Using from methods

You can create a new instance of `Money` using one of the following methods based on the type of the value.

#### From BigNumber

With the `from` method, a new instance of type `Money` is created from a `BigNumber` value.

```php
use TinyBlocks\Math\BigNumber;
use TinyBlocks\Currency\Currency;

$currency = Currency::USD;
$bigNumber = BigDecimal::from(value: '10');

Money::from(value: $bigNumber, currency: $currency);
```

#### From float

With the `fromFloat` method, a new instance of type `Money` is created from a `float` value. Note that floating point
values
are imprecise and may result in a loss of precision.

```php
Money::fromFloat(value: 10.00, currency: 'BRL');
```

#### From string

With the `fromString` method, a new instance of type `Money` is created from a `string` value.

```php
Money::fromString(value: '10.00', currency: 'BRL');
```

### Using the methods of mathematical operations

#### Addition

Performs an addition operation between this value and another value.

```php
use TinyBlocks\Currency\Currency;

$augend = Money::fromString(value: '100', currency: 'BRL');
$addend = Money::fromString(value: '1.50', currency: Currency::BRL->value);

$result = $augend->add(addend: $addend);
$result->amount->toString();

# Output: 101.50
```

#### Subtraction

Performs a subtraction operation between this value and another value.

```php
use TinyBlocks\Currency\Currency;

$minuend = Money::fromString(value: '10.50', currency: 'EUR');
$subtrahend = Money::fromString(value: '0.50', currency: Currency::EUR->value);

$result = $minuend->subtract(subtrahend: $subtrahend);
$result->amount->toString();

# Output: 10.00
```

#### Multiplication

Performs a multiplication operation between this value and another value.

```php
use TinyBlocks\Currency\Currency;

$multiplicand = Money::fromString(value: '5', currency: 'GBP');
$multiplier = Money::fromString(value: '3.12', currency: Currency::GBP->value);

$result = $multiplicand->multiply(multiplier: $multiplier);
$result->amount->toString(); 

# Output: 15.60
```

#### Division

Performs a division operation between this value and another value.

```php
use TinyBlocks\Currency\Currency;

$dividend = Money::fromString(value: '8.99', currency: 'CHF');
$divisor = Money::fromString(value: '5', currency: Currency::CHF->value);

$result = $dividend->divide(divisor: $divisor);
$result->amount->toString();

# Output: 1.79
```

<div id='license'></div>

## License

Money is licensed under [MIT](LICENSE).

<div id='contributing'></div>

## Contributing

Please follow the [contributing guidelines](https://github.com/tiny-blocks/tiny-blocks/blob/main/CONTRIBUTING.md) to
contribute to the project.
