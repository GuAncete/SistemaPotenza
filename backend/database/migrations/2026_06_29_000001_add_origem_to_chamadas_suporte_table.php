<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chamadas_suporte', function (Blueprint $table) {
            $table->dropForeign(['sessao_trabalho_id']);
            $table->dropForeign(['operario_id']);
            $table->dropForeign(['maquina_id']);

            $table->unsignedBigInteger('sessao_trabalho_id')->nullable()->change();
            $table->unsignedBigInteger('operario_id')->nullable()->change();
            $table->unsignedBigInteger('maquina_id')->nullable()->change();

            $table->foreign('sessao_trabalho_id')->references('id')->on('sessoes_trabalho')->cascadeOnDelete();
            $table->foreign('operario_id')->references('id')->on('operarios')->cascadeOnDelete();
            $table->foreign('maquina_id')->references('id')->on('maquinas')->cascadeOnDelete();

            $table->string('origem', 20)->default('operario')->after('maquina_id');
        });
    }

    public function down(): void
    {
        Schema::table('chamadas_suporte', function (Blueprint $table) {
            $table->dropColumn('origem');
        });
    }
};
