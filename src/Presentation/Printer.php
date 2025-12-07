<?php

declare(strict_types=1);

namespace Alexsoft\FeeCalculator\Presentation;

final readonly class Printer
{
    public function printSuccess(string $message): void
    {
        echo "{$message}\n";
    }

    public function printError(string $message): void
    {
        fwrite(STDERR, $message);
    }
}
