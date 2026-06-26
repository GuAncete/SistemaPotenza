<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordens_manutencao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maquina_id')->constrained('maquinas');
            $table->string('solicitante');
            $table->text('motivo');
            $table->string('status', 20)->default('aberta');
            $table->timestamp('solicitado_em')->useCurrent();
            $table->timestamp('atendido_em')->nullable();
            $table->timestamp('concluido_em')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index(['status', 'solicitado_em']);
            $table->index('maquina_id');
        });

        DB::statement(
            "ALTER TABLE ordens_manutencao ADD CONSTRAINT ordens_manutencao_status_check
             CHECK (status IN ('aberta', 'em_atendimento', 'concluida', 'cancelada'))"
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('ordens_manutencao');
    }
};
