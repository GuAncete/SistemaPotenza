<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EtapaProducao extends Model
{
    use HasFactory;

    protected $table = 'etapas_producao';

    protected $fillable = [
        'apontamento_id',
        'tipo',
        'inicio',
        'fim',
        'duracao_segundos',
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fim'    => 'datetime',
    ];

    public function apontamento(): BelongsTo
    {
        return $this->belongsTo(Apontamento::class);
    }

    public function calcularDuracao(): int
    {
        return (int) $this->inicio->diffInSeconds($this->fim);
    }
}
