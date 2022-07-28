<?php

namespace TinyBlocks\Money;

use TinyBlocks\Currency\Currency;
use TinyBlocks\Math\BigDecimal;
use TinyBlocks\Math\BigNumber;
use TinyBlocks\Money\Internal\Exceptions\DifferentCurrencies;
use TinyBlocks\Money\Internal\Exceptions\InvalidCurrencyScale;
use TinyBlocks\Vo\ValueObject;
use TinyBlocks\Vo\ValueObjectAdapter;

final class Money implements ValueObject
{
    use ValueObjectAdapter;

    private function __construct(public readonly BigNumber $amount, public readonly Currency $currency)
    {
        $withAmountScale = $this->amount->multiply(multiplier: BigDecimal::from(value: 1));

        if ($withAmountScale->getScale() > $this->currency->getDefaultFractionDigits()) {
            throw new InvalidCurrencyScale(amount: $withAmountScale, currency: $this->currency);
        }
    }

    public static function from(float|string|BigNumber $value, string|Currency $currency): Money
    {
        $currency = is_string($currency) ? Currency::from(value: $currency) : $currency;
        $amount = is_scalar($value) ? BigDecimal::from(value: $value) : $value;

        return new Money(amount: $amount, currency: $currency);
    }

    public function add(Money $addend): Money
    {
        if ($this->areCurrenciesDifferent(currency: $addend->currency)) {
            throw new DifferentCurrencies(currencyOne: $this->currency, currencyTwo: $addend->currency);
        }

        $result = $this->amount->add(addend: $addend->amount);

        return self::from(value: $result->toString(), currency: $this->currency);
    }

    public function subtract(Money $subtrahend): Money
    {
        if ($this->areCurrenciesDifferent(currency: $subtrahend->currency)) {
            throw new DifferentCurrencies(currencyOne: $this->currency, currencyTwo: $subtrahend->currency);
        }

        $result = $this->amount->subtract(subtrahend: $subtrahend->amount);

        return self::from(value: $result->toString(), currency: $this->currency);
    }

    public function multiply(Money $multiplier): Money
    {
        if ($this->areCurrenciesDifferent(currency: $multiplier->currency)) {
            throw new DifferentCurrencies(currencyOne: $this->currency, currencyTwo: $multiplier->currency);
        }

        $result = $this->amount->multiply(multiplier: $multiplier->amount);

        return self::from(value: $result->toString(), currency: $this->currency);
    }

    public function divide(Money $divisor): Money
    {
        if ($this->areCurrenciesDifferent(currency: $divisor->currency)) {
            throw new DifferentCurrencies(currencyOne: $this->currency, currencyTwo: $divisor->currency);
        }

        $result = $this->amount
            ->divide(divisor: $divisor->amount)
            ->withScale(scale: $this->currency->getDefaultFractionDigits());

        return self::from(value: $result->toString(), currency: $this->currency);
    }

    private function areCurrenciesDifferent(Currency $currency): bool
    {
        return $this->currency->value !== $currency->value;
    }
}
