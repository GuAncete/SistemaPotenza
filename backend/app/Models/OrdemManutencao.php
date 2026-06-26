<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdemManutencao extends Model
{
    protected $table = 'ordens_manutencao';

    protected $fillable = [
        'maquina_id',
        'solicitante',
        'motivo',
        'status',
        'solicitado_em',
        'atendido_em',
        'concluido_em',
        'observacoes',
    ];

    protected $casts = [
        'solicitado_em' => 'datetime',
        'atendido_em'   => 'datetime',
        'concluido_em'  => 'datetime',
    ];

    public function maquina(): BelongsTo
    {
        return $this->belongsTo(Maquina::class);
    }
}
