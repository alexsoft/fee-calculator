<?php

declare(strict_types=1);

namespace Alexsoft\FeeCalculator\Presentation;

use Alexsoft\FeeCalculator\Application\CalculateFeeCommand;
use Alexsoft\FeeCalculator\Business\Domain\TermMonths;
use Brick\Money\Money;
use InvalidArgumentException;
use NumberFormatter;

final readonly class CalculateFeeCommandMapper
{
    public function __construct(
        private NumberFormatter $numberFormatter,
    ) {}

    public function map(string $amountString, string $termString): CalculateFeeCommand
    {
        $amount = $this->numberFormatter->parse($amountString);

        if ($amount === false) {
            throw new InvalidArgumentException('Invalid amount string');
        }

        return new CalculateFeeCommand(
            Money::of($amount, 'EUR'),
            $this->createTermMonths($termString),
        );
    }

    private function createTermMonths(string $termString): TermMonths
    {
        $termMonths = TermMonths::tryFrom((int)$termString);

        if (
            !is_numeric($termString)
            || $termString != (int)$termString
            || $termMonths === null
        ) {
            throw new InvalidArgumentException(
                "Invalid term. Got [{$termString}]. Expected one of: " . implode(', ', TermMonths::getPossibleValues()),
            );
        }

        return $termMonths;
    }
}
