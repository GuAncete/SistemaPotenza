<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE maquinas ADD COLUMN prioridade VARCHAR(10) NOT NULL DEFAULT 'normal' CHECK (prioridade IN ('baixa', 'normal', 'alta', 'critica'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE maquinas DROP COLUMN IF EXISTS prioridade');
    }
};
