<?php

declare(strict_types=1);

namespace Alexsoft\Fee\Business\Domain;

enum TermMonths: int
{
    case Twelve = 12;
    case TwentyFour = 24;

    /**
     * @return list<positive-int>
     */
    public static function getPossibleValues(): array
    {
        return array_map(
            static fn(self $term): int => $term->value,
            self::cases(),
        );
    }
}
