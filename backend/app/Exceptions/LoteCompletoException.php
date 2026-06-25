<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class LoteCompletoException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $pilhasBipadas,
        public readonly int $totalPilhas,
    ) {
        parent::__construct($message);
    }
}
