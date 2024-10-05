<?php

declare(strict_types=1);

namespace TinyBlocks\Money;

use TinyBlocks\Currency\Currency;
use TinyBlocks\Math\BigDecimal;
use TinyBlocks\Math\BigNumber;
use TinyBlocks\Money\Internal\Exceptions\DifferentCurrencies;
use TinyBlocks\Money\Internal\Exceptions\InvalidCurrencyScale;
use TinyBlocks\Vo\ValueObject;
use TinyBlocks\Vo\ValueObjectAdapter;

final readonly class Money implements ValueObject
{
    use ValueObjectAdapter;

    private const ONE = 1;

    private function __construct(public BigNumber $amount, public Currency $currency)
    {
        $withAmountScale = $this->amount->multiply(multiplier: BigDecimal::from(value: self::ONE));

        if ($withAmountScale->getScale() > $this->currency->getFractionDigits()) {
            throw new InvalidCurrencyScale(amount: $withAmountScale, currency: $this->currency);
        }
    }

    public static function from(BigNumber $value, Currency $currency): Money
    {
        return new Money(amount: $value, currency: $currency);
    }

    public static function fromFloat(float $value, string $currency): Money
    {
        $amount = BigDecimal::from(value: $value);
        $currency = Currency::from(value: $currency);

        return new Money(amount: $amount, currency: $currency);
    }

    public static function fromString(string $value, string $currency): Money
    {
        $amount = BigDecimal::from(value: $value);
        $currency = Currency::from(value: $currency);

        return new Money(amount: $amount, currency: $currency);
    }

    public function add(Money $addend): Money
    {
        if ($this->areCurrenciesDifferent(currency: $addend->currency)) {
            throw new DifferentCurrencies(currencyOne: $this->currency, currencyTwo: $addend->currency);
        }

        $result = $this->amount->add(addend: $addend->amount);

        return self::fromString(value: $result->toString(), currency: $this->currency->value);
    }

    public function subtract(Money $subtrahend): Money
    {
        if ($this->areCurrenciesDifferent(currency: $subtrahend->currency)) {
            throw new DifferentCurrencies(currencyOne: $this->currency, currencyTwo: $subtrahend->currency);
        }

        $result = $this->amount->subtract(subtrahend: $subtrahend->amount);

        return self::fromString(value: $result->toString(), currency: $this->currency->value);
    }

    public function multiply(Money $multiplier): Money
    {
        if ($this->areCurrenciesDifferent(currency: $multiplier->currency)) {
            throw new DifferentCurrencies(currencyOne: $this->currency, currencyTwo: $multiplier->currency);
        }

        $result = $this->amount->multiply(multiplier: $multiplier->amount);

        return self::fromString(value: $result->toString(), currency: $this->currency->value);
    }

    public function divide(Money $divisor): Money
    {
        if ($this->areCurrenciesDifferent(currency: $divisor->currency)) {
            throw new DifferentCurrencies(currencyOne: $this->currency, currencyTwo: $divisor->currency);
        }

        $result = $this->amount
            ->divide(divisor: $divisor->amount)
            ->withScale(scale: $this->currency->getFractionDigits());

        return self::fromString(value: $result->toString(), currency: $this->currency->value);
    }

    private function areCurrenciesDifferent(Currency $currency): bool
    {
        return $this->currency->value !== $currency->value;
    }
}
