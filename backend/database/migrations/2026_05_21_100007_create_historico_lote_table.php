<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historico_lote', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etapa_fluxo_id')->constrained('etapas_fluxo')->cascadeOnDelete();
            $table->string('cod_peca');
            $table->string('ordem_lote');
            $table->unsignedInteger('total_pilhas')->default(0);
            $table->unsignedInteger('pilhas_concluidas')->default(0);
            $table->enum('status', ['em_andamento', 'concluido'])->default('em_andamento');
            $table->timestamp('entrada');
            $table->timestamp('saida')->nullable();
            $table->timestamps();

            $table->unique(['etapa_fluxo_id', 'ordem_lote'], 'unique_lote_por_etapa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historico_lote');
    }
};
