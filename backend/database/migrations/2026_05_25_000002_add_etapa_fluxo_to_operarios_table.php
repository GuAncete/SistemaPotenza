<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operarios', function (Blueprint $table) {
            $table->foreignId('etapa_fluxo_id')
                ->nullable()
                ->after('cargo')
                ->constrained('etapas_fluxo')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('operarios', function (Blueprint $table) {
            $table->dropForeign(['etapa_fluxo_id']);
            $table->dropColumn('etapa_fluxo_id');
        });
    }
};
