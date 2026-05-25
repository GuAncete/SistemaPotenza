<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apontamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sessao_trabalho_id')->constrained('sessoes_trabalho')->cascadeOnDelete();
            $table->foreignId('etapa_fluxo_id')->constrained('etapas_fluxo')->cascadeOnDelete();
            $table->string('cod_peca');
            $table->string('ordem_lote');
            $table->unsignedInteger('qtd_peca');
            $table->unsignedSmallInteger('pilha');
            $table->string('desc_peca')->nullable();
            $table->string('cod_produto')->nullable();
            $table->unsignedInteger('qtd_produzida')->nullable();
            $table->enum('status', ['em_setup', 'em_producao', 'finalizado'])->default('em_setup');
            $table->timestamps();

            // mesma pilha não pode ser bipada duas vezes na mesma etapa
            $table->unique(['etapa_fluxo_id', 'cod_peca', 'ordem_lote', 'pilha'], 'unique_pilha_por_etapa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apontamentos');
    }
};
