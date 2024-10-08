<?php

declare(strict_types=1);

namespace TinyBlocks\Money\Internal\Exceptions;

use RuntimeException;
use TinyBlocks\Currency\Currency;

final class DifferentCurrencies extends RuntimeException
{
    public function __construct(Currency $currencyOne, Currency $currencyTwo)
    {
        $template = 'Currencies <%s> and <%s> are different. ';
        $template .= 'The currencies must be the same to perform this operation.';
        parent::__construct(message: sprintf($template, $currencyOne->value, $currencyTwo->value));
    }
}
