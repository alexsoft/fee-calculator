<?php

declare(strict_types=1);

namespace Alexsoft\Fee\Presentation;

use Alexsoft\Fee\Application\CalculateFeeCommand;
use Alexsoft\Fee\Application\EurMoney;
use Alexsoft\Fee\Business\Domain\TermMonths;
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
            EurMoney::of($amount),
            $this->createTermMonths($termString),
        );
    }

    private function createTermMonths(string $termString): TermMonths
    {
        $termMonths = TermMonths::tryFrom((int)$termString);

        if ($termString != (int)$termString || $termMonths === null) {
            throw new InvalidArgumentException(
                "Invalid term. Got [{$termString}]. Expected one of: " . implode(', ', TermMonths::getPossibleValues()),
            );
        }

        return $termMonths;
    }
}
