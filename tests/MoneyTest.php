<?php

namespace TinyBlocks\Money;

use PHPUnit\Framework\TestCase;
use TinyBlocks\Currency\Currency;
use TinyBlocks\Math\BigDecimal;
use TinyBlocks\Math\PositiveBigDecimal;
use TinyBlocks\Money\Internal\Exceptions\DifferentCurrencies;
use TinyBlocks\Money\Internal\Exceptions\InvalidCurrencyScale;

final class MoneyTest extends TestCase
{
    /**
     * @dataProvider providerForTestFrom
     */
    public function testFrom(mixed $value, Currency $currency, string $expected): void
    {
        $actual = Money::from(value: $value, currency: $currency);

        self::assertEquals($expected, $actual->amount->toString());
        self::assertEquals($currency->value, $actual->currency->value);
    }

    public function testAdd(): void
    {
        $augend = Money::from(value: '100', currency: 'BRL');
        $addend = Money::from(value: '1.50', currency: Currency::BRL);

        $actual = $augend->add(addend: $addend);

        self::assertEquals('101.50', $actual->amount->toString());
        self::assertEquals(Currency::BRL->value, $actual->currency->value);
    }

    public function testSubtract(): void
    {
        $minuend = Money::from(value: '10.50', currency: 'EUR');
        $subtrahend = Money::from(value: '0.50', currency: Currency::EUR);

        $actual = $minuend->subtract(subtrahend: $subtrahend);

        self::assertEquals('10.00', $actual->amount->toString());
        self::assertEquals(Currency::EUR->value, $actual->currency->value);
    }

    public function testMultiply(): void
    {
        $multiplicand = Money::from(value: '5', currency: 'GBP');
        $multiplier = Money::from(value: '3.12', currency: Currency::GBP);

        $actual = $multiplicand->multiply(multiplier: $multiplier);

        self::assertEquals('15.60', $actual->amount->toString());
        self::assertEquals(Currency::GBP->value, $actual->currency->value);
    }

    public function testDivide(): void
    {
        $dividend = Money::from(value: '8.99', currency: 'CHF');
        $divisor = Money::from(value: '5', currency: Currency::CHF);

        $actual = $dividend->divide(divisor: $divisor);

        self::assertEquals('1.79', $actual->amount->toString());
        self::assertEquals(Currency::CHF->value, $actual->currency->value);
    }

    public function testInvalidCurrencyScale(): void
    {
        $template = 'The decimal scale <4> provided for currency <BRL> is invalid. ';
        $template .= 'The scale must be less than or equal to <2>.';

        $this->expectException(InvalidCurrencyScale::class);
        $this->expectExceptionMessage($template);

        Money::from(value: 10.1234, currency: Currency::BRL);
    }

    public function testAddingDifferentCurrencies(): void
    {
        $template = 'Currencies <BRL> and <USD> are different. ';
        $template .= 'The currencies must be the same to perform this operation.';

        $this->expectException(DifferentCurrencies::class);
        $this->expectExceptionMessage($template);

        $augend = Money::from(value: 100, currency: Currency::BRL);
        $addend = Money::from(value: '1.50', currency: Currency::USD);

        $augend->add(addend: $addend);
    }

    public function testSubtractionDifferentCurrencies(): void
    {
        $template = 'Currencies <BRL> and <EUR> are different. ';
        $template .= 'The currencies must be the same to perform this operation.';

        $this->expectException(DifferentCurrencies::class);
        $this->expectExceptionMessage($template);

        $minuend = Money::from(value: 100, currency: Currency::BRL);
        $subtrahend = Money::from(value: '1.50', currency: Currency::EUR);

        $minuend->subtract(subtrahend: $subtrahend);
    }

    public function testMultiplicationDifferentCurrencies(): void
    {
        $template = 'Currencies <BRL> and <GBP> are different. ';
        $template .= 'The currencies must be the same to perform this operation.';

        $this->expectException(DifferentCurrencies::class);
        $this->expectExceptionMessage($template);

        $multiplicand = Money::from(value: 100, currency: Currency::BRL);
        $multiplier = Money::from(value: '1.50', currency: Currency::GBP);

        $multiplicand->multiply(multiplier: $multiplier);
    }

    public function testDivisionDifferentCurrencies(): void
    {
        $template = 'Currencies <BRL> and <CHF> are different. ';
        $template .= 'The currencies must be the same to perform this operation.';

        $this->expectException(DifferentCurrencies::class);
        $this->expectExceptionMessage($template);

        $dividend = Money::from(value: 100, currency: Currency::BRL);
        $divisor = Money::from(value: '1.50', currency: Currency::CHF);

        $dividend->divide(divisor: $divisor);
    }

    public function providerForTestFrom(): array
    {
        return [
            [
                'value'    => 1,
                'currency' => Currency::XPF,
                'expected' => '1'
            ],
            [
                'value'    => '100.12',
                'currency' => Currency::USD,
                'expected' => '100.12'
            ],
            [
                'value'    => BigDecimal::from(value: 999.12),
                'currency' => Currency::BRL,
                'expected' => '999.12'
            ],
            [
                'value'    => PositiveBigDecimal::from(value: '9.123'),
                'currency' => Currency::TND,
                'expected' => '9.123'
            ],
            [
                'value'    => '9.1234',
                'currency' => Currency::CLF,
                'expected' => '9.1234'
            ]
        ];
    }
}
