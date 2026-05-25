<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessoes_trabalho', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operario_id')->constrained('operarios')->cascadeOnDelete();
            $table->foreignId('maquina_id')->constrained('maquinas')->cascadeOnDelete();
            $table->timestamp('inicio');
            $table->timestamp('fim')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessoes_trabalho');
    }
};
