<?php

declare(strict_types=1);

namespace TinyBlocks\Money;

use TinyBlocks\Currency\Currency;
use TinyBlocks\Math\BigNumber;
use TinyBlocks\Money\Internal\Exceptions\DifferentCurrencies;

/**
 * Represents operations that can be performed on monetary values,
 * ensuring the same currency is used for all arithmetic operations.
 */
interface Monetary
{
    /**
     * Adds the given Monetary amount to the current one.
     *
     * @param Monetary $addend The amount to add.
     * @return Monetary The result of the addition.
     * @throws DifferentCurrencies If the currencies of the amounts are different.
     */
    public function add(Monetary $addend): Monetary;

    /**
     * Divides the current Monetary amount by the given divisor.
     *
     * @param Monetary $divisor The divisor amount.
     * @return Monetary The result of the division.
     * @throws DifferentCurrencies If the currencies of the amounts are different.
     */
    public function divide(Monetary $divisor): Monetary;

    /**
     * Creates a new Monetary object from the given BigNumber and Currency.
     *
     * @param BigNumber $value The monetary value.
     * @param Currency $currency The currency associated with the monetary value.
     * @return Monetary A new Monetary object.
     */
    public static function from(BigNumber $value, Currency $currency): Monetary;

    /**
     * Creates a new Monetary object from the given float and currency code.
     *
     * @param float $value The monetary value as a float.
     * @param string $currency The currency code (e.g., "USD").
     * @return Monetary A new Monetary object.
     */
    public static function fromFloat(float $value, string $currency): Monetary;

    /**
     * Creates a new Monetary object from the given string and currency code.
     *
     * @param string $value The monetary value as a string.
     * @param string $currency The currency code (e.g., "USD").
     * @return Monetary A new Monetary object.
     */
    public static function fromString(string $value, string $currency): Monetary;

    /**
     * Gets the monetary value.
     *
     * @return BigNumber The amount as a BigNumber.
     */
    public function getAmount(): BigNumber;

    /**
     * Gets the currency of the monetary value.
     *
     * @return Currency The associated currency.
     */
    public function getCurrency(): Currency;

    /**
     * Multiplies the current Monetary amount by the given multiplier.
     *
     * @param Monetary $multiplier The multiplier amount.
     * @return Monetary The result of the multiplication.
     * @throws DifferentCurrencies If the currencies of the amounts are different.
     */
    public function multiply(Monetary $multiplier): Monetary;

    /**
     * Subtracts the given Monetary amount from the current one.
     *
     * @param Monetary $subtrahend The amount to subtract.
     * @return Monetary The result of the subtraction.
     * @throws DifferentCurrencies If the currencies of the amounts are different.
     */
    public function subtract(Monetary $subtrahend): Monetary;
}
