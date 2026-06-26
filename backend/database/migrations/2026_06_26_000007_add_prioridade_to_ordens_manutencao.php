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
    Schema::table('ordens_manutencao', function (Blueprint $table) {
        if (!Schema::hasColumn('ordens_manutencao', 'prioridade')) {
            $table->string('prioridade', 20)->default('normal')->after('motivo');
        }
    });

    // Adiciona o constraint só se não existir
    DB::statement("
        DO \$\$ BEGIN
            IF NOT EXISTS (
                SELECT 1 FROM pg_constraint 
                WHERE conname = 'ordens_manutencao_prioridade_check'
            ) THEN
                ALTER TABLE ordens_manutencao 
                ADD CONSTRAINT ordens_manutencao_prioridade_check 
                CHECK (prioridade IN ('baixa', 'normal', 'alta', 'critica'));
            END IF;
        END \$\$;
    ");
}
};
