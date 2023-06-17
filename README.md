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

### Using the from method

With the `from` method, a new instance of type `Money` is created from a valid numeric value. You can provide
a `string`, `float` or `BigNumber` value.

```php
Money::from(value: 10, currency: 'BRL');
Money::from(value: '10', currency: Currency::USD);
Money::from(value: BigDecimal::from(value: '10'), currency: Currency::USD);
```

Floating point values instantiated from a `float` may not be safe, as they are imprecise by design and may result in a
loss of precision. Always prefer to instantiate from a `string`, which supports an unlimited amount digits.

### Using the methods of mathematical operations

#### Addition

Performs an addition operation between this value and another value.

```php
$augend = Money::from(value: '100', currency: 'BRL');
$addend = Money::from(value: '1.50', currency: Currency::BRL);

$result = $augend->add(addend: $addend);

$result->amount->toString(); # 101.50
```

#### Subtraction

Performs a subtraction operation between this value and another value.

```php
$minuend = Money::from(value: '10.50', currency: 'EUR');
$subtrahend = Money::from(value: '0.50', currency: Currency::EUR);

$result = $minuend->subtract(subtrahend: $subtrahend);

$result->amount->toString(); # 10.00
```

#### Multiplication

Performs a multiplication operation between this value and another value.

```php
$multiplicand = Money::from(value: '5', currency: 'GBP');
$multiplier = Money::from(value: '3.12', currency: Currency::GBP);

$result = $multiplicand->multiply(multiplier: $multiplier);

$result->amount->toString(); # 15.60
```

#### Division

Performs a division operation between this value and another value.

```php
$dividend = Money::from(value: '8.99', currency: 'CHF');
$divisor = Money::from(value: '5', currency: Currency::CHF);

$result = $dividend->divide(divisor: $divisor);

$result->amount->toString(); # 1.79
```

<div id='license'></div>

## License

Money is licensed under [MIT](LICENSE).

<div id='contributing'></div>

## Contributing

Please follow the [contributing guidelines](https://github.com/tiny-blocks/tiny-blocks/blob/main/CONTRIBUTING.md) to
contribute to the project.
