<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Operario extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'matricula',
        'cargo',
        'etapa_fluxo_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function etapaFluxo(): BelongsTo
    {
        return $this->belongsTo(EtapaFluxo::class);
    }

    public function sessoesTrabalho(): HasMany
    {
        return $this->hasMany(SessaoTrabalho::class);
    }

    public function sessaoAtiva(): HasOne
    {
        return $this->hasOne(SessaoTrabalho::class)->whereNull('fim');
    }
}
