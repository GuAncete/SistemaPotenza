<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maquinas', function (Blueprint $table) {
            $table->string('codigo', 50)->nullable()->unique()->after('nome');
            $table->smallInteger('ano')->unsigned()->nullable()->after('codigo');
            $table->string('foto')->nullable()->after('descricao');
        });
    }

    public function down(): void
    {
        Schema::table('maquinas', function (Blueprint $table) {
            $table->dropColumn(['codigo', 'ano', 'foto']);
        });
    }
};
