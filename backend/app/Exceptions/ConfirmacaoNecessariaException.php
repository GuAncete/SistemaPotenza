<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class ConfirmacaoNecessariaException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $passagensRealizadas,
        public readonly int $passagensEsperadas,
    ) {
        parent::__construct($message);
    }
}
