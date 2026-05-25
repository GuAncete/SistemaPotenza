<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etapas_fluxo', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->unsignedSmallInteger('ordem');
            $table->boolean('ativa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etapas_fluxo');
    }
};
