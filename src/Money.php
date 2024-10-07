<?php

declare(strict_types=1);

namespace TinyBlocks\Money;

use TinyBlocks\Currency\Currency;
use TinyBlocks\Math\BigDecimal;
use TinyBlocks\Math\BigNumber;
use TinyBlocks\Money\Internal\Exceptions\DifferentCurrencies;
use TinyBlocks\Money\Internal\Exceptions\InvalidCurrencyScale;

readonly class Money implements Monetary
{
    private const ONE = 1.0;

    protected function __construct(public BigNumber $amount, public Currency $currency)
    {
        $withAmountScale = $this->amount->multiply(multiplier: BigDecimal::fromFloat(value: self::ONE));

        if ($withAmountScale->getScale() > $this->currency->getFractionDigits()) {
            throw new InvalidCurrencyScale(amount: $withAmountScale, currency: $this->currency);
        }
    }

    public static function from(BigNumber $value, Currency $currency): static
    {
        return new static(amount: $value, currency: $currency);
    }

    public static function fromFloat(float $value, string $currency): static
    {
        $amount = BigDecimal::fromFloat(value: $value);
        $currency = Currency::from(value: $currency);

        return new static(amount: $amount, currency: $currency);
    }

    public static function fromString(string $value, string $currency): static
    {
        $amount = BigDecimal::fromString(value: $value);
        $currency = Currency::from(value: $currency);

        return new static(amount: $amount, currency: $currency);
    }

    public function add(Monetary $addend): static
    {
        $currency = $addend->getCurrency();

        if ($this->areCurrenciesDifferent(currency: $currency)) {
            throw new DifferentCurrencies(currencyOne: $this->currency, currencyTwo: $currency);
        }

        $result = $this->amount->add(addend: $addend->getAmount());

        return self::fromString(value: $result->toString(), currency: $this->currency->value);
    }

    public function divide(Monetary $divisor): static
    {
        $currency = $divisor->getCurrency();

        if ($this->areCurrenciesDifferent(currency: $currency)) {
            throw new DifferentCurrencies(currencyOne: $this->currency, currencyTwo: $currency);
        }

        $result = $this->amount
            ->divide(divisor: $divisor->getAmount())
            ->withScale(scale: $this->currency->getFractionDigits());

        return self::fromString(value: $result->toString(), currency: $this->currency->value);
    }

    public function multiply(Monetary $multiplier): static
    {
        $currency = $multiplier->getCurrency();

        if ($this->areCurrenciesDifferent(currency: $currency)) {
            throw new DifferentCurrencies(currencyOne: $this->currency, currencyTwo: $currency);
        }

        $result = $this->amount->multiply(multiplier: $multiplier->getAmount());

        return self::fromString(value: $result->toString(), currency: $this->currency->value);
    }

    public function subtract(Monetary $subtrahend): static
    {
        $currency = $subtrahend->getCurrency();

        if ($this->areCurrenciesDifferent(currency: $currency)) {
            throw new DifferentCurrencies(currencyOne: $this->currency, currencyTwo: $currency);
        }

        $result = $this->amount->subtract(subtrahend: $subtrahend->getAmount());

        return self::fromString(value: $result->toString(), currency: $this->currency->value);
    }

    public function getAmount(): BigNumber
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    private function areCurrenciesDifferent(Currency $currency): bool
    {
        return $this->currency->value !== $currency->value;
    }
}
