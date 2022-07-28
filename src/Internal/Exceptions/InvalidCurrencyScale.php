<?php

namespace TinyBlocks\Money\Internal\Exceptions;

use RuntimeException;
use TinyBlocks\Currency\Currency;
use TinyBlocks\Math\BigNumber;

final class InvalidCurrencyScale extends RuntimeException
{
    public function __construct(BigNumber $amount, Currency $currency)
    {
        $template = 'The decimal scale <%s> provided for currency <%s> is invalid. ';
        $template .= 'The scale must be less than or equal to <%s>.';
        parent::__construct(
            sprintf(
                $template,
                $amount->getScale(),
                $currency->name,
                $currency->getDefaultFractionDigits()
            )
        );
    }
}
