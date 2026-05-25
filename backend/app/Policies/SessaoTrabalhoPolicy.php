<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SessaoTrabalho;
use App\Models\User;

class SessaoTrabalhoPolicy
{
    public function update(User $user, SessaoTrabalho $sessao): bool
    {
        return $user->operario?->id === $sessao->operario_id;
    }
}
