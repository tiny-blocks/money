<?php

declare(strict_types=1);

namespace TinyBlocks\Money\Models;

use TinyBlocks\Math\BigDecimal;
use TinyBlocks\Money\Monetary;
use TinyBlocks\Money\Money;

final readonly class DiscountedMoney extends Money
{
    public function applyDiscount(float $discountPercentage): Monetary
    {
        $discountFactor = BigDecimal::fromFloat(value: 1 - ($discountPercentage / 100));
        $discountedAmount = $this->amount->multiply(multiplier: $discountFactor);

        return DiscountedMoney::fromString(value: $discountedAmount->toString(), currency: $this->currency->value);
    }
}
