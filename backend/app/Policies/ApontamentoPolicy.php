<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Apontamento;
use App\Models\User;

class ApontamentoPolicy
{
    public function view(User $user, Apontamento $apontamento): bool
    {
        if ($user->isGestor()) {
            return true;
        }

        return $user->operario?->id === $apontamento->sessaoTrabalho->operario_id;
    }

    public function update(User $user, Apontamento $apontamento): bool
    {
        return $user->operario?->id === $apontamento->sessaoTrabalho->operario_id;
    }
}
