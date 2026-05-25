<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoricoLote extends Model
{
    use HasFactory;

    protected $table = 'historico_lote';

    protected $fillable = [
        'etapa_fluxo_id',
        'cod_peca',
        'ordem_lote',
        'total_pilhas',
        'pilhas_concluidas',
        'status',
        'entrada',
        'saida',
    ];

    protected $casts = [
        'entrada' => 'datetime',
        'saida'   => 'datetime',
    ];

    public function etapaFluxo(): BelongsTo
    {
        return $this->belongsTo(EtapaFluxo::class);
    }

    public function percentualConcluido(): int
    {
        if ($this->total_pilhas === 0) {
            return 0;
        }

        return (int) round(($this->pilhas_concluidas / $this->total_pilhas) * 100);
    }
}
