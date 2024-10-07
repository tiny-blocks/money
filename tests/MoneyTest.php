<?php

declare(strict_types=1);

namespace TinyBlocks\Money;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TinyBlocks\Currency\Currency;
use TinyBlocks\Math\BigDecimal;
use TinyBlocks\Math\BigNumber;
use TinyBlocks\Math\PositiveBigDecimal;
use TinyBlocks\Money\Internal\Exceptions\DifferentCurrencies;
use TinyBlocks\Money\Internal\Exceptions\InvalidCurrencyScale;
use TinyBlocks\Money\Models\DiscountedMoney;

final class MoneyTest extends TestCase
{
    #[DataProvider('floatDataProvider')]
    public function testFromFloat(float $value, string $currency, float $expected): void
    {
        /** @Given a value and a currency */
        $actual = Money::fromFloat(value: $value, currency: $currency);

        /** @Then the amount and currency should match the expected values */
        self::assertSame($expected, $actual->amount->toFloat());
        self::assertSame($currency, $actual->currency->value);
    }

    #[DataProvider('stringDataProvider')]
    public function testFromString(string $value, string $currency, string $expected): void
    {
        /** @Given a value and a currency */
        $actual = Money::fromString(value: $value, currency: $currency);

        /** @Then the amount and currency should match the expected values */
        self::assertSame($expected, $actual->amount->toString());
        self::assertSame($currency, $actual->currency->value);
    }

    #[DataProvider('bigNumberDataProvider')]
    public function testFromBigNumber(BigNumber $value, Currency $currency, string $expected): void
    {
        /** @Given a BigNumber and a currency */
        $actual = Money::from(value: $value, currency: $currency);

        /** @Then the amount and currency should match the expected values */
        self::assertSame($expected, $actual->amount->toString());
        self::assertSame($currency->value, $actual->currency->value);
    }

    public function testAdd(): void
    {
        /** @Given two Money objects with the same currency */
        $augend = Money::fromString(value: '100', currency: Currency::BRL->value);
        $addend = Money::fromString(value: '1.50', currency: Currency::BRL->value);

        /** @When adding them */
        $actual = $augend->add(addend: $addend);

        /** @Then the result should be the sum of the amounts with the same currency */
        self::assertSame('101.50', $actual->amount->toString());
        self::assertSame(Currency::BRL->value, $actual->currency->value);
    }

    public function testSubtract(): void
    {
        /** @Given two Money objects with the same currency */
        $minuend = Money::fromString(value: '10.50', currency: Currency::EUR->value);
        $subtrahend = Money::fromString(value: '0.50', currency: Currency::EUR->value);

        /** @When subtracting them */
        $actual = $minuend->subtract(subtrahend: $subtrahend);

        /** @Then the result should be the difference of the amounts with the same currency */
        self::assertSame('10.00', $actual->amount->toString());
        self::assertSame(Currency::EUR->value, $actual->currency->value);
    }

    public function testMultiply(): void
    {
        /** @Given two Money objects with the same currency */
        $multiplicand = Money::fromString(value: '5', currency: Currency::GBP->value);
        $multiplier = Money::fromString(value: '3.12', currency: Currency::GBP->value);

        /** @When multiplying them */
        $actual = $multiplicand->multiply(multiplier: $multiplier);

        /** @Then the result should be the product of the amounts with the same currency */
        self::assertSame('15.60', $actual->amount->toString());
        self::assertSame(Currency::GBP->value, $actual->currency->value);
    }

    public function testDivide(): void
    {
        /** @Given two Money objects with the same currency */
        $dividend = Money::fromString(value: '8.99', currency: Currency::CHF->value);
        $divisor = Money::fromString(value: '5', currency: Currency::CHF->value);

        /** @When dividing them */
        $actual = $dividend->divide(divisor: $divisor);

        /** @Then the result should be the quotient of the amounts with the same currency */
        self::assertSame('1.79', $actual->amount->toString());
        self::assertSame(Currency::CHF->value, $actual->currency->value);
    }

    public function testInvalidCurrencyScale(): void
    {
        /** @Given a Money object with an invalid scale for the currency */
        $template = 'The decimal scale <4> provided for currency <BRL> is invalid. ';
        $template .= 'The scale must be less than or equal to <2>.';

        /** @Then an InvalidCurrencyScale exception should be thrown */
        $this->expectException(InvalidCurrencyScale::class);
        $this->expectExceptionMessage($template);

        /** @When creating the Money object */
        Money::fromFloat(value: 10.1234, currency: Currency::BRL->value);
    }

    public function testAddingDifferentCurrencies(): void
    {
        /** @Given two Money objects with different currencies */
        $template = 'Currencies <BRL> and <USD> are different. ';
        $template .= 'The currencies must be the same to perform this operation.';

        /** @Then a DifferentCurrencies exception should be thrown */
        $this->expectException(DifferentCurrencies::class);
        $this->expectExceptionMessage($template);

        /** @When trying to add them */
        $augend = Money::fromFloat(value: 100, currency: Currency::BRL->value);
        $addend = Money::fromString(value: '1.50', currency: Currency::USD->value);

        $augend->add(addend: $addend);
    }

    public function testSubtractionDifferentCurrencies(): void
    {
        /** @Given two Money objects with different currencies */
        $template = 'Currencies <BRL> and <EUR> are different. ';
        $template .= 'The currencies must be the same to perform this operation.';

        /** @Then a DifferentCurrencies exception should be thrown */
        $this->expectException(DifferentCurrencies::class);
        $this->expectExceptionMessage($template);

        /** @When trying to subtract them */
        $minuend = Money::fromFloat(value: 100, currency: Currency::BRL->value);
        $subtrahend = Money::fromString(value: '1.50', currency: Currency::EUR->value);

        $minuend->subtract(subtrahend: $subtrahend);
    }

    public function testMultiplicationDifferentCurrencies(): void
    {
        /** @Given two Money objects with different currencies */
        $template = 'Currencies <BRL> and <GBP> are different. ';
        $template .= 'The currencies must be the same to perform this operation.';

        /** @Then a DifferentCurrencies exception should be thrown */
        $this->expectException(DifferentCurrencies::class);
        $this->expectExceptionMessage($template);

        /** @When trying to multiply them */
        $multiplicand = Money::fromFloat(value: 100, currency: Currency::BRL->value);
        $multiplier = Money::fromString(value: '1.50', currency: Currency::GBP->value);

        $multiplicand->multiply(multiplier: $multiplier);
    }

    public function testDivisionDifferentCurrencies(): void
    {
        /** @Given two Money objects with different currencies */
        $template = 'Currencies <BRL> and <CHF> are different. ';
        $template .= 'The currencies must be the same to perform this operation.';

        /** @Then a DifferentCurrencies exception should be thrown */
        $this->expectException(DifferentCurrencies::class);
        $this->expectExceptionMessage($template);

        /** @When trying to divide them */
        $dividend = Money::fromFloat(value: 100, currency: Currency::BRL->value);
        $divisor = Money::fromString(value: '1.50', currency: Currency::CHF->value);

        $dividend->divide(divisor: $divisor);
    }

    public function testExtendedMoneyClass(): void
    {
        /** @Given a Money object with a specific amount and currency */
        $money = DiscountedMoney::fromFloat(value: 100.00, currency: Currency::USD->value);

        /** @When applying a discount */
        $actual = $money->applyDiscount(discountPercentage: 10.00);

        /** @Then the result should reflect the discounted amount */
        self::assertSame('90.0', $actual->amount->toString());
        self::assertSame(Currency::USD->value, $actual->currency->value);
    }

    public function testOperationBetweenMoneyAndDiscountedMoney(): void
    {
        /** @Given a Money object and a DiscountedMoney object with the same currency */
        $money = Money::fromFloat(value: 200.05, currency: Currency::USD->value);
        $discountedMoney = DiscountedMoney::fromFloat(value: 100.05, currency: Currency::USD->value);

        /** @When adding them */
        $actual = $money->add(addend: $discountedMoney);

        /** @Then the result should be the sum of the amounts with the same currency */
        self::assertSame(300.10, $actual->amount->toFloat());
        self::assertSame('300.10', $actual->amount->toString());
        self::assertSame(Currency::USD->value, $actual->currency->value);
    }

    public static function floatDataProvider(): array
    {
        return [
            'Integer value'            => [
                'value'    => 1,
                'currency' => Currency::XPF->value,
                'expected' => 1
            ],
            'Negative value'           => [
                'value'    => -50.00,
                'currency' => Currency::EUR->value,
                'expected' => -50.00
            ],
            'High scale currency'      => [
                'value'    => 9.1234,
                'currency' => Currency::CLF->value,
                'expected' => 9.1234
            ],
            'Float decimal value'      => [
                'value'    => 100.12,
                'currency' => Currency::USD->value,
                'expected' => 100.12
            ],
            'Value with leading zeros' => [
                'value'    => 001.23,
                'currency' => Currency::USD->value,
                'expected' => 001.23
            ]
        ];
    }

    public static function stringDataProvider(): array
    {
        return [
            'Integer value'            => [
                'value'    => '1',
                'currency' => Currency::XPF->value,
                'expected' => '1'
            ],
            'Negative value'           => [
                'value'    => '-50.00',
                'currency' => Currency::EUR->value,
                'expected' => '-50.00'
            ],
            'High scale currency'      => [
                'value'    => '9.1234',
                'currency' => Currency::CLF->value,
                'expected' => '9.1234'
            ],
            'String decimal value'     => [
                'value'    => '100.12',
                'currency' => Currency::USD->value,
                'expected' => '100.12'
            ],
            'Value with leading zeros' => [
                'value'    => '001.23',
                'currency' => Currency::USD->value,
                'expected' => '001.23'
            ]
        ];
    }

    public static function bigNumberDataProvider(): array
    {
        return [
            'Big decimal value'          => [
                'value'    => BigDecimal::fromFloat(value: 999.12),
                'currency' => Currency::BRL,
                'expected' => '999.12'
            ],
            'Positive big decimal value' => [
                'value'    => PositiveBigDecimal::fromString(value: '9.123'),
                'currency' => Currency::TND,
                'expected' => '9.123'
            ]
        ];
    }
}
