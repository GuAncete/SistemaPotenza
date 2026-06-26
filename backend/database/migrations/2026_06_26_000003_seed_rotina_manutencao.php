<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('rotinas')->where('slug', 'manutencao')->exists()) {
            return;
        }

        $maxOrdem = (int) (DB::table('rotinas')->whereNull('parent_id')->max('ordem') ?? 0);

        DB::table('rotinas')->insert([
            'nome'       => 'Manutenção',
            'slug'       => 'manutencao',
            'pagina'     => '/admin/manutencao',
            'icone'      => 'Wrench',
            'parent_id'  => null,
            'ordem'      => $maxOrdem + 1,
            'ativo'      => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('rotinas')->where('slug', 'manutencao')->delete();
    }
};
