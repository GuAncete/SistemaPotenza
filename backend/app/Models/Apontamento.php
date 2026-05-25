<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Apontamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'sessao_trabalho_id',
        'etapa_fluxo_id',
        'cod_peca',
        'ordem_lote',
        'qtd_peca',
        'pilha',
        'desc_peca',
        'cod_produto',
        'qtd_produzida',
        'status',
    ];

    public function sessaoTrabalho(): BelongsTo
    {
        return $this->belongsTo(SessaoTrabalho::class);
    }

    public function etapaFluxo(): BelongsTo
    {
        return $this->belongsTo(EtapaFluxo::class);
    }

    public function etapasProducao(): HasMany
    {
        return $this->hasMany(EtapaProducao::class);
    }

    public function etapaSetup(): HasOne
    {
        return $this->hasOne(EtapaProducao::class)->where('tipo', 'setup');
    }

    public function etapaProducao(): HasOne
    {
        return $this->hasOne(EtapaProducao::class)->where('tipo', 'producao');
    }

    public function isAtivo(): bool
    {
        return in_array($this->status, ['em_setup', 'em_producao'], true);
    }
}
