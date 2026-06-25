<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apontamentos', function (Blueprint $table) {
            $table->unsignedTinyInteger('numero_passagem')->default(1)->after('total_pausa_segundos');
            $table->foreignId('apontamento_origem_id')
                ->nullable()
                ->after('numero_passagem')
                ->constrained('apontamentos')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('apontamentos', function (Blueprint $table) {
            $table->dropForeign(['apontamento_origem_id']);
            $table->dropColumn(['numero_passagem', 'apontamento_origem_id']);
        });
    }
};
